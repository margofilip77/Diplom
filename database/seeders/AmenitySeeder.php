<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Amenity;
use App\Models\AmenityCategory;

class AmenitySeeder extends Seeder
{
    public function run()
    {
        // Спочатку очищаємо дочірню таблицю
        Amenity::query()->delete();

        // Тепер можна очистити головну таблицю
        AmenityCategory::query()->delete();

        // Додаємо категорії зручностей
        $categories = [
            ['id' => 1, 'category_name' => 'Основні зручності'],
            ['id' => 2, 'category_name' => 'Домашні тварини'],
            ['id' => 3, 'category_name' => 'Ванна кімната'],
            ['id' => 4, 'category_name' => 'Комфорт у спальнях'],
            ['id' => 5, 'category_name' => 'На відкритому повітрі'],
            ['id' => 6, 'category_name' => 'Кухня та приготування їжі'],
            ['id' => 7, 'category_name' => 'Зручності в номері'],
            ['id' => 8, 'category_name' => 'Спорт і активний відпочинок'],
            ['id' => 9, 'category_name' => 'Розваги та медіа'],
            ['id' => 10, 'category_name' => 'Їжа та напої'],
            ['id' => 11, 'category_name' => 'Безпека'],
            ['id' => 12, 'category_name' => 'Загальні умови'],
            ['id' => 13, 'category_name' => 'Оздоровчі послуги'],
            ['id' => 14, 'category_name' => 'Мова спілкування'],
        ];

        DB::table('amenity_categories')->insert($categories);

        // Додаємо зручності
        $amenities = [
            ['name' => 'Безкоштовний Wi-Fi', 'category_id' => 1],
            ['name' => 'Кондиціонер', 'category_id' => 1],
            ['name' => 'Опалення', 'category_id' => 1],
            ['name' => 'Сімейні номери', 'category_id' => 1],
            ['name' => 'Номери для некурців', 'category_id' => 1],
            ['name' => 'Можна з хатніми тваринами', 'category_id' => 2],
            ['name' => 'Власна ванна кімната', 'category_id' => 3],
            ['name' => 'Ванна', 'category_id' => 3],
            ['name' => 'Душ', 'category_id' => 3],
            ['name' => 'Туалет', 'category_id' => 3],
            ['name' => 'Рушники', 'category_id' => 3],
            ['name' => 'Фен', 'category_id' => 3],
            ['name' => 'Туалетний папір', 'category_id' => 3],
            ['name' => 'Білизна', 'category_id' => 4],
            ['name' => 'Шафа або гардероб', 'category_id' => 4],
            ['name' => 'Тераса', 'category_id' => 5],
            ['name' => 'Сад', 'category_id' => 5],
            ['name' => 'Місце для пікніка', 'category_id' => 5],
            ['name' => 'Садові меблі', 'category_id' => 5],
            ['name' => 'Камін в саду', 'category_id' => 5],
            ['name' => 'Кухня', 'category_id' => 6],
            ['name' => 'Міні-кухня', 'category_id' => 6],
            ['name' => 'Мікрохвильова піч', 'category_id' => 6],
            ['name' => 'Холодильник', 'category_id' => 6],
            ['name' => 'Електрочайник', 'category_id' => 6],
            ['name' => 'Кухонний посуд', 'category_id' => 6],
            ['name' => 'Барбекю', 'category_id' => 6],
            ['name' => 'Розетка поблизу з ліжком', 'category_id' => 7],
            ['name' => 'Вішалка для одягу', 'category_id' => 7],
            ['name' => 'Прокат лижного спорядження', 'category_id' => 8],
            ['name' => 'Приміщення для зберігання лиж', 'category_id' => 8],
            ['name' => 'Катання на лижах', 'category_id' => 8],
            ['name' => 'Велоспорт', 'category_id' => 8],
            ['name' => 'Оренда велосипедів', 'category_id' => 8],
            ['name' => 'Плавання під водою з маскою', 'category_id' => 8],
            ['name' => 'Дартс', 'category_id' => 8],
            ['name' => 'Риболовля', 'category_id' => 8],
            ['name' => 'Телевізор із плоским екраном', 'category_id' => 9],
            ['name' => 'Супутникові канали', 'category_id' => 9],
            ['name' => 'Жива музика', 'category_id' => 9],
            ['name' => 'Настільні ігри / пазли', 'category_id' => 9],
            ['name' => 'Дитячий майданчик', 'category_id' => 9],
            ['name' => 'Бар', 'category_id' => 10],
            ['name' => 'Сніданок у номері', 'category_id' => 10],
            ['name' => 'Фрукти', 'category_id' => 10],
            ['name' => 'Вино/шампанське', 'category_id' => 10],
            ['name' => 'Чайник/кавоварка', 'category_id' => 10],
            ['name' => 'Вогнегасники', 'category_id' => 11],
            ['name' => 'Охоронна сигналізація', 'category_id' => 11],
            ['name' => 'Вхід з ключем', 'category_id' => 11],
            ['name' => 'Цілодобова охорона', 'category_id' => 11],
            ['name' => 'Місця для куріння', 'category_id' => 12],
            ['name' => 'Запаковані ланчі', 'category_id' => 12],
            ['name' => 'Церква/храм', 'category_id' => 12],
            ['name' => 'Сауна', 'category_id' => 13],
            ['name' => 'Масаж', 'category_id' => 13],
            ['name' => 'Пляжне крісло/шезлонг', 'category_id' => 13],
            ['name' => 'Українська', 'category_id' => 14],
            ['name' => 'Англійська', 'category_id' => 14],
            ['name' => 'Польська', 'category_id' => 14],
            ['name' => 'Російська', 'category_id' => 14],
            ['name' => 'Французька', 'category_id' => 14],
        ];

        DB::table('amenities')->insert($amenities);
    }
}
