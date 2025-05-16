@extends('layouts.app')

@section('content')
<div class="container my-5">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

    <div class="cart-container shadow-sm" style="background: #FFFFFF; border-radius: 20px; padding: 30px;">
        @if(empty($cart->accommodations) || $cart->accommodations->isEmpty())
        <div class="empty-cart text-center py-5">
            <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #FF6200;"></i>
            <h3 class="fw-bold" style="color: #1A1A1A;">Ваш кошик порожній</h3>
            <p class="text-muted">Додайте помешкання для початку планування!</p>
            <a href="{{ route('accommodations.index') }}" class="btn" style="background: #1A73E8; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Переглянути помешкання</a>
        </div>
        @else
        @foreach($cart->accommodations as $key => $accommodation)
        @php
        $guests = is_string($accommodation->guests_count) ? json_decode($accommodation->guests_count, true) : $accommodation->guests_count;
        $checkinDate = \Carbon\Carbon::parse($accommodation->checkin_date);
        $checkoutDate = \Carbon\Carbon::parse($accommodation->checkout_date);
        $nights = max(1, abs($checkoutDate->diffInDays($checkinDate)));
        $mealTotal = $accommodation->mealOptions->sum(function ($cartMealOption) {
        return max(0, ($cartMealOption->price ?? 0)) * max(1, $cartMealOption->guests_count);
        });
        $itemTotal = $accommodation->itemTotal ?? (max(0, $accommodation->price) * $nights + $mealTotal);
        $itemId = isset($accommodation->id) ? $accommodation->id : $key;
        $regionId = isset($accommodation->accommodation->city->region_id) ? $accommodation->accommodation->city->region_id : $accommodation->accommodation->region_id;
        @endphp

        <div class="cart-item mb-4 p-3" style="border-bottom: 1px solid #E5E7EB;" data-id="{{ $itemId }}">
            <div class="d-flex align-items-start">
                <img src="{{ asset($accommodation->accommodation_photo) }}" alt="{{ isset($accommodation->accommodation) ? $accommodation->accommodation->name : 'Помешкання' }}" style="width: 200px; height: 120px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                <div class="item-details flex-grow-1 ms-4">
                    <h3 class="fw-bold mb-2" style="font-size: 1.2rem; color: #1A1A1A;">{{ isset($accommodation->accommodation) ? $accommodation->accommodation->name : 'Помешкання' }}</h3>
                    <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                        <i class="fas fa-calendar-alt me-1" style="color: #FF6200;"></i>
                        {{ $accommodation->checkin_date }} - {{ $accommodation->checkout_date }} ({{ $nights }} ночей)
                    </p>
                    @if(!empty($accommodation->mealOptions))
                    <div class="mt-2">
                        <h4 style="font-size: 1rem; color: #1A73E8;">Обране харчування:</h4>
                        @foreach($accommodation->mealOptions as $cartMealOption)
                        @php
                        $meal = $cartMealOption->mealOption ?? (object) ['name' => 'Невідомий тип'];
                        $price = max(0, $cartMealOption->price ?? 0);
                        @endphp
                        <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                            {{ $meal->name }} ({{ $cartMealOption->guests_count }} гостей) - {{ $price }} грн/гостя, всього {{ $price * $cartMealOption->guests_count }} грн
                        </p>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="button-group d-flex flex-column gap-2 align-items-end">
                    <a href="{{ route('accommodations.show', isset($accommodation->accommodation) ? $accommodation->accommodation->id : $accommodation->accommodation_id) }}" class="btn" style="background: #1A73E8; color: #FFFFFF; border-radius: 20px; padding: 8px 16px; font-size: 0.9rem;">Деталі</a>
                    <a href="{{ route('cart.remove', $itemId) }}" class="btn btn-remove" style="background: #FF6200; color: #FFFFFF; border-radius: 20px; padding: 8px 16px; font-size: 0.9rem;" onclick="return confirm('Ви впевнені, що хочете видалити цей елемент із кошика?')">Видалити</a>
                    <p class="item-price mt-2" style="font-size: 1rem; color: #1A73E8; font-weight: 600;" data-base-price="{{ (max(0, $accommodation->price) * $nights) + $mealTotal }}">{{ $itemTotal }} грн</p>
                    <p class="service-total mt-1" style="font-size: 0.9rem; color: #6B7280; display: none;">Додаткові послуги: <span class="service-total-amount">0</span> грн</p>
                    <p class="package-total mt-1" style="font-size: 0.9rem; color: #6B7280; display: none;">Пакети: <span class="package-total-amount">0</span> грн</p>
                </div>
            </div>

            <div class="additional-services mt-3">
                <h4 style="font-size: 1rem; color: #1A73E8;">Оберіть додаткові послуги</h4>
                <div class="category-filter mb-3 d-flex align-items-center">
                    <label for="category-select-{{ $itemId }}" class="me-2" style="color: #6B7280;">Фільтр за категорією:</label>
                    <select id="category-select-{{ $itemId }}" class="form-select w-auto" onchange="filterServices('{{ $itemId }}')">
                        <option value="all">Усі категорії</option>
                        @foreach($accommodation->serviceCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($accommodation->availableServices->isNotEmpty())
                <div class="row services-container" id="services-{{ $itemId }}">
    @foreach($accommodation->availableServices as $categoryId => $services)
    @foreach($services as $service)
    <div class="col-md-4 mb-3 service-item" data-category="{{ $service->category_id }}">
        <div class="card h-100" style="border-radius: 15px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
            <img src="{{ asset('services/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}" style="height: 150px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <div class="card-body p-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input service-checkbox"
                        id="service_{{ $service->id }}_{{ $itemId }}"
                        data-service-id="{{ $service->id }}"
                        data-price="{{ $service->price }}"
                        data-latitude="{{ $service->latitude ?? $accommodation->accommodation->latitude ?? 50.4501 }}"
                        data-longitude="{{ $service->longitude ?? $accommodation->accommodation->longitude ?? 30.5234 }}"
                        onchange="updateCartTotal('{{ $itemId }}')">
                    <label class="form-check-label" for="service_{{ $service->id }}_{{ $itemId }}">
                        <span style="font-weight: 600; color: #1A1A1A;">{{ $service->name }} ({{ $service->price }} грн)</span>
                    </label>
                </div>
                <p class="text-muted mt-2" style="font-size: 0.85rem;">{{ Str::limit($service->description, 100) }}</p>
                @if($service->distance !== null)
                <p class="text-muted" style="font-size: 0.85rem;">
                    <i class="fas fa-map-marker-alt me-1" style="color: #FF6200;"></i>
                    Відстань: {{ $service->distance }} км
                </p>
                @else
                <p class="text-muted" style="font-size: 0.85rem;">Відстань недоступна</p>
                @endif
                <button class="btn btn-sm view-more-btn" style="background: #1A73E8; color: #FFFFFF;"
                    data-service-id="{{ $service->id }}"
                    data-name="{{ addslashes($service->name) }}"
                    data-description="{{ addslashes($service->description) }}"
                    data-price="{{ $service->price }}"
                    data-image="{{ asset('services/' . $service->image) }}"
                    data-latitude="{{ $service->latitude ?? $accommodation->accommodation->latitude ?? 50.4501 }}"
                    data-longitude="{{ $service->longitude ?? $accommodation->accommodation->longitude ?? 30.5234 }}"
                    onclick="showServiceModal(this)">Переглянути більше</button>
            </div>
        </div>
    </div>
    @endforeach
    @endforeach
</div>
                @else
                <p class="text-muted">Немає доступних послуг для цього регіону.</p>
                @endif
            </div>

            <div class="additional-packages mt-3">
                <h4 style="font-size: 1rem; color: #1A73E8;">Оберіть пакети послуг</h4>
                @if($packages->where('region_id', $regionId)->isNotEmpty())
                <div class="row packages-container" id="packages-{{ $itemId }}">
                    @foreach($packages->where('region_id', $regionId) as $package)
                    <div class="col-md-4 mb-3 package-item">
                        <div class="card h-100" style="border-radius: 15px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="form-check me-2">
                                        <input type="checkbox" class="form-check-input package-checkbox"
                                            id="package_{{ $package->id }}_{{ $itemId }}"
                                            data-package-id="{{ $package->id }}"
                                            data-price="{{ $package->calculatePrice() }}"
                                            onchange="updateCartTotal('{{ $itemId }}')">
                                        <label class="form-check-label" for="package_{{ $package->id }}_{{ $itemId }}">
                                            <span style="font-weight: 600; color: #1A1A1A;">{{ $package->name }}</span>
                                        </label>
                                    </div>
                                    <span class="badge bg-green-100 text-green-800 text-xs">Еко-тур</span>
                                </div>
                                <p class="text-muted mt-2" style="font-size: 0.85rem;">{{ Str::limit($package->description, 100) }}</p>
                                @if($package->services->isNotEmpty())
                                <p class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Включено:</p>
                                <ul class="list-unstyled" style="font-size: 0.85rem;">
                                    @foreach($package->services as $service)
                                    <li class="mb-1 d-flex align-items-center">
                                        <img src="{{ asset('services/' . $service->image) }}" alt="{{ $service->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 5px; margin-right: 8px;">
                                        <span>{{ $service->name }} ({{ $service->price }} грн)</span>
                                        @if(str_contains(strtolower($service->name), 'трансфер'))
                                        <i class="fas fa-car ms-2" style="color: #28a745;"></i>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted" style="font-size: 0.85rem;">Послуги не додані до пакета.</p>
                                @endif
                                @if($package->discount > 0)
                                <p class="text-muted line-through" style="font-size: 0.8rem;">До знижки: {{ $package->originalPrice() }} грн</p>
                                <p style="font-size: 0.9rem; color: #1A73E8; font-weight: 600;">Після знижки ({{ $package->discount }}%): {{ $package->calculatePrice() }} грн</p>
                                @else
                                <p style="font-size: 0.9rem; color: #1A73E8; font-weight: 600;">Ціна: {{ $package->calculatePrice() }} грн</p>
                                @endif
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm" style="background: #1A73E8; color: #FFFFFF;"
                                        data-package-id="{{ $package->id }}"
                                        data-name="{{ addslashes($package->name) }}"
                                        data-description="{{ addslashes($package->description) }}"
                                        data-price="{{ $package->calculatePrice() }}"
                                        data-discount="{{ $package->discount }}"
                                        data-original-price="{{ $package->originalPrice() }}"
                                        data-services="{{ json_encode($package->services->map(fn($service) => ['name' => $service->name, 'price' => $service->price, 'image' => asset('services/' . $service->image)])) }}"
                                        onclick="showPackageModal(this)">Детальніше</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Немає доступних пакетів для цього регіону.</p>
                @endif
            </div>
        </div>
        @endforeach

        <div class="text-end mt-4">
            <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="GET" style="display: inline;">
                <input type="hidden" id="cartData" name="cartData">
                <button type="submit" class="btn" style="background: #28a745; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;" onclick="saveCartData(event)">Оформити замовлення</button>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Modal for service details -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Деталі послуги</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="modal-service-image" src="" alt="Service Image" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;">
                        <div id="service-map" style="height: 200px; width: 100%; margin-top: 10px;"></div>
                    </div>
                    <div class="col-md-6">
                        <h3 id="modal-service-name" class="mb-3"></h3>
                        <p id="modal-service-description" class="text-muted mb-3"></p>
                        <p class="mb-2"><strong>Ціна:</strong> <span id="modal-service-price"></span> грн</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for package details -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="packageModalLabel">Деталі пакета</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 id="modal-package-name" class="mb-3"></h3>
                        <p id="modal-package-description" class="text-muted mb-3"></p>
                        <p class="mb-2"><strong>Ціна:</strong> <span id="modal-package-price"></span> грн</p>
                        <p class="mb-2 original-price" style="display: none;"><strong>До знижки:</strong> <span id="modal-package-original-price"></span> грн</p>
                        <p class="mb-2 discount" style="display: none;"><strong>Знижка:</strong> <span id="modal-package-discount"></span>%</p>
                        <p class="mb-2"><strong>Включені послуги:</strong></p>
                        <ul id="modal-package-services" class="list-unstyled" style="font-size: 0.9rem;"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let currentMap = null; // Змінна для зберігання поточної карти

    // Update cart total
    function updateCartTotal(accommodationId) {
        const itemElement = document.querySelector(`.cart-item[data-id="${accommodationId}"]`);
        if (!itemElement) return;

        const priceElement = itemElement.querySelector('.item-price');
        const serviceTotalElement = itemElement.querySelector('.service-total');
        const serviceTotalAmountElement = itemElement.querySelector('.service-total-amount');
        const packageTotalElement = itemElement.querySelector('.package-total');
        const packageTotalAmountElement = itemElement.querySelector('.package-total-amount');

        const basePrice = parseFloat(priceElement.getAttribute('data-base-price')) || 0;
        let serviceTotal = 0;
        let packageTotal = 0;
        let selectedServices = [];
        let selectedPackages = [];

        // Збір обраних послуг
        const checkedServices = itemElement.querySelectorAll('.service-checkbox:checked');
        checkedServices.forEach(checkbox => {
            const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
            const serviceId = checkbox.getAttribute('data-service-id');
            const label = checkbox.nextElementSibling.querySelector('span').textContent;
            serviceTotal += price;
            selectedServices.push({
                id: serviceId,
                name: label.split('(')[0].trim(),
                price: price
            });
        });

        // Збір обраних пакетів
        const checkedPackages = itemElement.querySelectorAll('.package-checkbox:checked');
        checkedPackages.forEach(checkbox => {
            const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
            const packageId = checkbox.getAttribute('data-package-id');
            const label = checkbox.nextElementSibling.querySelector('span').textContent;
            packageTotal += price;
            selectedPackages.push({
                id: packageId,
                name: label.trim(),
                price: price
            });
        });

        // Оновлення відображення сум
        if (serviceTotal > 0) {
            serviceTotalAmountElement.textContent = serviceTotal.toFixed(2);
            serviceTotalElement.style.display = 'block';
        } else {
            serviceTotalElement.style.display = 'none';
        }

        if (packageTotal > 0) {
            packageTotalAmountElement.textContent = packageTotal.toFixed(2);
            packageTotalElement.style.display = 'block';
        } else {
            packageTotalElement.style.display = 'none';
        }

        // Оновлення загальної суми
        const totalPrice = basePrice + serviceTotal + packageTotal;
        priceElement.textContent = totalPrice.toFixed(2) + ' грн';

        // Збереження в localStorage
        const cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        cartData[accommodationId] = {
            services: selectedServices,
            packages: selectedPackages,
            service_total: serviceTotal,
            package_total: packageTotal,
            base_price: basePrice,
            total_price: totalPrice
        };
        localStorage.setItem('cartData', JSON.stringify(cartData));
        console.log('Updated cartData:', cartData);
    }

    // Save cart data before checkout
    function saveCartData(event) {
        event.preventDefault(); // Запобігаємо стандартному відправленню форми
        const cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        document.querySelectorAll('.cart-item').forEach(item => {
            const accommodationId = item.getAttribute('data-id');
            updateCartTotal(accommodationId);
        });

        // Оновлюємо cartData перед передачею
        const finalCartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        document.getElementById('cartData').value = JSON.stringify(finalCartData);
        console.log('Saved cartData for checkout:', finalCartData);

        // Відправляємо форму
        document.getElementById('checkoutForm').submit();
    }

    // Filter services by category
    function filterServices(accommodationId) {
        const selectedCategory = document.getElementById(`category-select-${accommodationId}`).value;
        const services = document.querySelectorAll(`#services-${accommodationId} .service-item`);

        services.forEach(service => {
            const categoryId = service.getAttribute('data-category');
            service.style.display = (selectedCategory === 'all' || categoryId === selectedCategory) ? 'block' : 'none';
        });

        updateCartTotal(accommodationId);
    }

    // Show service modal with map
    function showServiceModal(button) {
        const serviceData = {
            id: button.getAttribute('data-service-id'),
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            price: button.getAttribute('data-price'),
            image: button.getAttribute('data-image'),
            latitude: parseFloat(button.getAttribute('data-latitude')),
            longitude: parseFloat(button.getAttribute('data-longitude'))
        };

        document.getElementById('modal-service-name').textContent = serviceData.name;
        document.getElementById('modal-service-description').textContent = serviceData.description;
        document.getElementById('modal-service-price').textContent = serviceData.price;
        document.getElementById('modal-service-image').src = serviceData.image;

        // Очищаємо попередню карту, якщо вона існує
        const mapElement = document.getElementById('service-map');
        if (currentMap) {
            currentMap.remove(); // Видаляємо попередню карту
            currentMap = null;
        }

        // Перевіряємо, чи координати є валідними
        if (isNaN(serviceData.latitude) || isNaN(serviceData.longitude)) {
            console.error('Invalid coordinates:', serviceData.latitude, serviceData.longitude);
            mapElement.innerHTML = '<p class="text-danger">Неможливо відобразити карту: координати недоступні.</p>';
            const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
            modal.show();
            return;
        }

        // Ініціалізація нової карти
        if (mapElement) {
            currentMap = L.map('service-map').setView([serviceData.latitude, serviceData.longitude], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(currentMap);
            L.marker([serviceData.latitude, serviceData.longitude]).addTo(currentMap)
                .bindPopup(serviceData.name)
                .openPopup();
        }

        const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
        modal.show();

        // Оновлюємо розмір карти після відображення модального вікна
        document.getElementById('serviceModal').addEventListener('shown.bs.modal', function () {
            if (currentMap) {
                currentMap.invalidateSize(); // Перераховуємо розмір карти
            }
        }, { once: true });
    }

    // Show package modal
    function showPackageModal(button) {
        const packageData = {
            id: button.getAttribute('data-package-id'),
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            price: button.getAttribute('data-price'),
            originalPrice: button.getAttribute('data-original-price'),
            discount: parseFloat(button.getAttribute('data-discount')),
            services: JSON.parse(button.getAttribute('data-services'))
        };

        document.getElementById('modal-package-name').textContent = packageData.name;
        document.getElementById('modal-package-description').textContent = packageData.description;
        document.getElementById('modal-package-price').textContent = packageData.price;

        const originalPriceElement = document.querySelector('.original-price');
        const discountElement = document.querySelector('.discount');
        if (packageData.discount > 0) {
            document.getElementById('modal-package-original-price').textContent = packageData.originalPrice;
            document.getElementById('modal-package-discount').textContent = packageData.discount;
            originalPriceElement.style.display = 'block';
            discountElement.style.display = 'block';
        } else {
            originalPriceElement.style.display = 'none';
            discountElement.style.display = 'none';
        }

        const servicesList = document.getElementById('modal-package-services');
        servicesList.innerHTML = '';
        packageData.services.forEach(service => {
            const li = document.createElement('li');
            li.className = 'mb-1 d-flex align-items-center';
            li.innerHTML = `
                <img src="${service.image}" alt="${service.name}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 5px; margin-right: 8px;">
                <span>${service.name} (${service.price} грн)</span>
                ${service.name.toLowerCase().includes('трансфер') ? '<i class="fas fa-car ms-2" style="color: #28a745;"></i>' : ''}
            `;
            servicesList.appendChild(li);
        });

        const modal = new bootstrap.Modal(document.getElementById('packageModal'));
        modal.show();
    }

    // Clean up invalid cart data
    function cleanCartData() {
        const cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        const validIds = Array.from(document.querySelectorAll('.cart-item')).map(item => item.getAttribute('data-id'));
        Object.keys(cartData).forEach(id => {
            if (!validIds.includes(id)) {
                delete cartData[id];
            }
        });
        localStorage.setItem('cartData', JSON.stringify(cartData));
        console.log('Cleaned cartData:', cartData);
    }

    // Remove item from localStorage
    function removeCartItem(accommodationId) {
        let cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        if (cartData[accommodationId]) {
            delete cartData[accommodationId];
            localStorage.setItem('cartData', JSON.stringify(cartData));
        }
    }

    // Add package to localStorage
    function addPackageToCart(accommodationId, packageData) {
        let cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        if (!cartData[accommodationId]) {
            cartData[accommodationId] = {
                services: [],
                packages: [],
                service_total: 0,
                package_total: 0,
                base_price: 0,
                total_price: 0
            };
        }
        const existingPackage = cartData[accommodationId].packages.find(p => p.id === packageData.id);
        if (!existingPackage) {
            cartData[accommodationId].packages.push(packageData);
            cartData[accommodationId].package_total = (cartData[accommodationId].package_total || 0) + packageData.price;
            localStorage.setItem('cartData', JSON.stringify(cartData));
        }
    }

    // Remove package from localStorage
    function removePackageFromCart(accommodationId, packageId, packagePrice) {
        let cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        if (cartData[accommodationId]) {
            cartData[accommodationId].packages = cartData[accommodationId].packages.filter(p => p.id !== packageId);
            cartData[accommodationId].package_total = (cartData[accommodationId].package_total || 0) - packagePrice;
            if (cartData[accommodationId].packages.length === 0 && cartData[accommodationId].services.length === 0) {
                delete cartData[accommodationId];
            }
            localStorage.setItem('cartData', JSON.stringify(cartData));
        }
    }

    // DOMContentLoaded event
    document.addEventListener("DOMContentLoaded", function() {
        // Hover effects
        document.querySelectorAll(".cart-item").forEach(item => {
            item.addEventListener("mouseenter", () => {
                const card = item.querySelector('.card');
                if (card) card.style.transform = "scale(1.02)";
            });
            item.addEventListener("mouseleave", () => {
                const card = item.querySelector('.card');
                if (card) card.style.transform = "scale(1)";
            });
        });

        // Clean up invalid cart data
        cleanCartData();

        // Restore selected services and packages
        const cartData = JSON.parse(localStorage.getItem('cartData') || '{}');
        Object.keys(cartData).forEach(accommodationId => {
            const services = cartData[accommodationId]?.services || [];
            const packages = cartData[accommodationId]?.packages || [];
            services.forEach(service => {
                const checkbox = document.getElementById(`service_${service.id}_${accommodationId}`);
                if (checkbox) checkbox.checked = true;
            });
            packages.forEach(package => {
                const checkbox = document.getElementById(`package_${package.id}_${accommodationId}`);
                if (checkbox) checkbox.checked = true;
            });
            updateCartTotal(accommodationId);
        });

        // Filter services
        document.querySelectorAll('.category-filter select').forEach(select => {
            filterServices(select.id.replace('category-select-', ''));
        });

        // Handle remove buttons
        document.querySelectorAll('.btn-remove').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                if (!confirm('Ви впевнені, що хочете видалити цей елемент із кошика?')) return;

                const accommodationId = this.closest('.cart-item').getAttribute('data-id');
                const url = this.getAttribute('href');

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Видаляємо з localStorage
                            removeCartItem(accommodationId);
                            // Оновлюємо сторінку
                            window.location.reload();
                        } else {
                            alert(data.message || 'Помилка при видаленні елемента');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Виникла помилка при видаленні елемента');
                    });
            });
        });

        // Handle package checkboxes
        document.querySelectorAll('.package-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const accommodationId = this.closest('.cart-item').getAttribute('data-id');
                const packageId = this.getAttribute('data-package-id');
                const price = parseFloat(this.getAttribute('data-price'));
                const label = this.nextElementSibling.querySelector('span').textContent;

                if (this.checked) {
                    const packageData = {
                        id: packageId,
                        name: label.trim(),
                        price: price
                    };
                    addPackageToCart(accommodationId, packageData);
                } else {
                    removePackageFromCart(accommodationId, packageId, price);
                }
                updateCartTotal(accommodationId);
            });
        });

        // Handle service checkboxes
        document.querySelectorAll('.service-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const accommodationId = this.closest('.cart-item').getAttribute('data-id');
                updateCartTotal(accommodationId);
            });
        });
    });
</script>

<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        z-index: auto;
        margin-top: 80px;
    }

    .cart-item:hover .card {
        background: #F9FAFB;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-cart .btn:hover {
        background: #1557B0;
    }

    .card:hover {
        transform: scale(1.02);
    }

    .modal-content {
        border-radius: 15px;
    }

    .service-item,
    .package-item {
        transition: all 0.3s ease;
    }

    .service-checkbox:checked+.form-check-label,
    .package-checkbox:checked+.form-check-label {
        color: #1A73E8;
    }

    .line-through {
        text-decoration: line-through;
        color: #6B7280;
    }

    .badge.bg-green-100 {
        background-color: #D1FAE5;
    }

    .badge.text-green-800 {
        color: #065F46;
    }

    #service-map {
        border-radius: 8px;
        border: 1px solid #ddd;
        overflow: hidden; /* Додано для уникнення обрізання */
    }
</style>
@endsection