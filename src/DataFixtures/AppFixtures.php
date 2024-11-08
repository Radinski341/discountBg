<?php

namespace App\DataFixtures;

use App\Factory\BaseCategoryFactory;
use App\Factory\BaseSubcategoryFactory;
use App\Factory\MainCategoryFactory;
use App\Factory\UserFactory;
use App\Factory\WebsiteDeliveryRoleFactory;
use App\Factory\WebsiteFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email' => 'radinskikire@gmail.com', 'roles' => ['ROLE_OWNER']]);
        UserFactory::createOne(['email' => 'headadmin@gmail.com', 'roles' => ['ROLE_HEAD_ADMIN']]);
        UserFactory::createOne(['email' => 'admin@gmail.com', 'roles' => ['ROLE_ADMIN']]);

        $websitePraktiker = WebsiteFactory::createOne(['websiteName' => 'praktiker', 'freeDeliveryOver' => 200]);

        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 0, 'max' => 15, 'deliveryPrice' => 4.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 15, 'max' => 20, 'deliveryPrice' => 7.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 20, 'max' => 85, 'deliveryPrice' => 9.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 85, 'max' => 150, 'deliveryPrice' => 19.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 150, 'max' => 200, 'deliveryPrice' => 29.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websitePraktiker, 'min' => 200, 'max' => 99999, 'deliveryPrice' => 0]);

        $websiteBricolage = WebsiteFactory::createOne(['websiteName' => 'bricolage', 'freeDeliveryOver' => 300]);

        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 0, 'max' => 40, 'deliveryPrice' => 4.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 40, 'max' => 70, 'deliveryPrice' => 7.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 70, 'max' => 100, 'deliveryPrice' => 9.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 100, 'max' => 150, 'deliveryPrice' => 14.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 150, 'max' => 200, 'deliveryPrice' => 19.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 200, 'max' => 250, 'deliveryPrice' => 29.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 250, 'max' => 300, 'deliveryPrice' => 39.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteBricolage, 'min' => 300, 'max' => 99999, 'deliveryPrice' => 0]);

        $websiteMakasa = WebsiteFactory::createOne(['websiteName' => 'makasa', 'freeDeliveryOver' => 60]);

        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteMakasa, 'min' => 0, 'max' => 60, 'deliveryPrice' => 5.99]);
        WebsiteDeliveryRoleFactory::createOne(['website' => $websiteMakasa, 'min' => 60, 'max' => 99999, 'deliveryPrice' => 0]);

        $emagWebsites = ['emag1', 'emag2'];

        $fixedPrices = [
            ['min' => 0, 'max' => 30, 'deliveryPrice' => 3.99],
            ['min' => 30, 'max' => 60, 'deliveryPrice' => 7.99],
            ['min' => 60, 'max' => 90, 'deliveryPrice' => 11.99],
            ['min' => 90, 'max' => 120, 'deliveryPrice' => 15.99],
            ['min' => 120, 'max' => 150, 'deliveryPrice' => 19.99],
            ['min' => 150, 'max' => 180, 'deliveryPrice' => 23.99],
            ['min' => 180, 'max' => 210, 'deliveryPrice' => 27.99],
            ['min' => 210, 'max' => 240, 'deliveryPrice' => 31.99],
            ['min' => 240, 'max' => 270, 'deliveryPrice' => 34.99],
            ['min' => 270, 'max' => 300, 'deliveryPrice' => 39.99],
            ['min' => 300, 'max' => 99999, 'deliveryPrice' => 0]
        ];

        foreach ($emagWebsites as $emag) {
            $website = WebsiteFactory::createOne(['websiteName' => $emag, 'freeDeliveryOver' => 300]);

            foreach ($fixedPrices as $price) {
                WebsiteDeliveryRoleFactory::createOne([
                    'website' => $website,
                    'min' => $price['min'],
                    'max' => $price['max'],
                    'deliveryPrice' => $price['deliveryPrice']
                ]);
            }
        }

        $baseCategoryData = [
            ['Инструменти и железария', 'bricolage', 'Инструменти'],
            ['Инструменти и железария', 'bricolage', 'Железария'],
            ['Инструменти и железария', 'praktiker', 'Инструменти и железария'],
            ['Строителство', 'bricolage', 'Строителство'],
            ['Строителство', 'praktiker', 'Строителни материали'],
            ['Осветление', 'bricolage', 'Осветление'],
            ['Осветление', 'praktiker', 'Осветление'],
            ['Двор и градина', 'bricolage', 'Двор и градина'],
            ['Двор и градина', 'praktiker', 'Градина'],
            ['Мебели и интериор', 'praktiker', 'Мебели и интериор'],
            ['Мебели и интериор', 'bricolage', 'Интериор'],
            ['Отопление и охлаждане', 'bricolage', 'Отопление и охлаждане'],
            ['Отопление и охлаждане', 'praktiker', 'Отопление, Охлаждане и Вик'],
            ['Баня и плочки', 'bricolage', 'Баня'],
            ['Баня и плочки', 'praktiker', 'Баня'],
            ['Електричество', 'bricolage', 'Електричество'],
            ['Подови настилки', 'bricolage', 'Подови настилки'],
            ['Бои и лакове', 'bricolage', 'Бои'],
            ['Бои и лакове', 'praktiker', 'Бои и лакове'],
            ['Кухня', 'bricolage', 'Кухня'],
            ['Кухня', 'praktiker', 'Кухня'],
            ['Стълби', 'bricolage', 'Стълби'],
            ['Авто и вело', 'bricolage', 'Авто и вело'],
            ['Печки и камини', 'bricolage', 'Печки и камини'],
            ['Крепежни елементи', 'praktiker', 'Крепежни елементи'],
            ['Почистване', 'praktiker', 'Почистване'],
            ['Лепила', 'bricolage', 'Лепила'],
            ['Лепила', 'praktiker', 'Лепила'],
            ['Басейни, джакузита', 'bricolage', 'Басейни, джакузита'],
            ['Врати и прозорци', 'praktiker', 'Врати и прозорци'],
            ['Обков', 'praktiker', 'Обков'],
            ['Техника и уреди', 'bricolage', 'Техника и уреди'],
            ['Огради', 'bricolage', 'Огради'],
        ];

        foreach ($baseCategoryData as $category) {
            BaseCategoryFactory::new()->create([
                'title' => $category[0],
                'website' => $category[1],
                'replaceCategory' => $category[2],
            ]);
        }

        $baseSubcategoriesData = [
            ['Електрически и акумулаторни инструменти', 'praktiker', 'Инструменти и железария', 'Винтоверт'],
            ['Електрически и акумулаторни инструменти', 'praktiker', 'Инструменти и железария', 'Акумулаторни комплекти'],
            ['Електрически и акумулаторни инструменти', 'praktiker', 'Инструменти и железария', 'Пробивни машини'],
            ['Електрически и акумулаторни инструменти', 'praktiker', 'Инструменти и железария', 'Триони и циркуляри'],
            ['Шлифоване и рязане', 'praktiker', 'Инструменти и железария', 'Шлайф машини'],
            ['Големи машини и оборудване', 'praktiker', 'Инструменти и железария', 'Големи машини'],
            ['Почистване и водно оборудване', 'praktiker', 'Инструменти и железария', 'Прахосмукачки за сухо и мокро'],
            ['Почистване и водно оборудване', 'praktiker', 'Инструменти и железария', 'Водоструйки'],
            ['Почистване и водно оборудване', 'praktiker', 'Инструменти и железария', 'Помпи'],
            ['Градински инструменти и машини', 'praktiker', 'Инструменти и железария', 'Градински машини'],
            ['Градински инструменти и машини', 'praktiker', 'Инструменти и железария', 'Градински ръчен инструмент'],
            ['Мебели и обков', 'praktiker', 'Инструменти и железария', 'Стелажи'],
            ['Електрически и акумулаторни инструменти', 'bricolage', '', 'Електропреносими инструменти'],
            ['Шлифоване и рязане', 'bricolage', 'Инструменти и железария', 'Инструменти за плочки и вик'],
            ['Големи машини и оборудване', 'bricolage', 'Инструменти и железария', 'Големи (настолни) инструменти'],
            ['Големи машини и оборудване', 'bricolage', 'Инструменти и железария', 'Компресори и пневматични инструменти'],
            ['Почистване и водно оборудване', 'bricolage', 'Инструменти и железария', 'Почистващи инструменти'],
            ['Градински инструменти и машини', 'bricolage', 'Инструменти и железария', 'Ръчни инструменти'],
            ['Мебели и обков', 'bricolage', 'Инструменти и железария', 'Мебелен обков'],
            ['Мебели и обков', 'bricolage', 'Инструменти и железария', 'Обков за врати и прозорци'],
            ['Мебели и обков', 'bricolage', 'Инструменти и железария', 'Стелажи и шкафове'],
            ['Автомобилни и велосипедни аксесоари', 'bricolage', 'Инструменти и железария', 'Автоаксесоари'],
            ['Автомобилни и велосипедни аксесоари', 'bricolage', 'Инструменти и железария', 'Велосипеди и части'],
            ['Опаковане и сигурност', 'bricolage', 'Инструменти и железария', 'Опаковане и преместване'],
            ['Опаковане и сигурност', 'bricolage', 'Инструменти и железария', 'Сигурност'],
            ['Строителни материали', 'praktiker', 'Строителство', 'Мазилки, грундове и лакове'],
            ['Строителни материали', 'praktiker', 'Строителство', 'Сухи строителни смеси'],
            ['ВИК и отводняване', 'praktiker', 'Строителство', 'Материали за покриви'],
            ['ВИК и отводняване', 'praktiker', 'Строителство', 'Сухи строителни смеси'],
            ['ВИК и отводняване', 'praktiker', 'Строителство', 'Мазилки, грундове и лакове'],
            ['ВИК и отводняване', 'praktiker','Строителство', 'Вик'],
            ['ВИК и отводняване', 'praktiker','Строителство', 'Улуци, тръби и канали'],
            ['Инструменти за плочки и шпакловане', 'praktiker','Строителство', 'Машини за рязане на плочки'],
            ['Инструменти за плочки и шпакловане', 'praktiker','Строителство', 'Инструменти, аксесоари за шпакловане'],
            ['Уплътнители и лепила', 'praktiker','Строителство', 'Уплътнители и фуги'],
            ['Врати и прозорци', 'praktiker','Строителство', 'Прозорци'],
            ['Врати и прозорци', 'praktiker','Строителство', 'Врати и обков'],
            ['Стълби и скелета', 'praktiker','Строителство', 'Строителни стълби и скелета'],
            ['Уплътнители и лепила', 'bricolage','Строителство', 'Ленти'],
            ['Уплътнители и лепила', 'bricolage','Строителство', 'Лепила'],
            ['Строителни материали', 'bricolage','Строителство', 'Дървен материал'],
            ['Строителни материали', 'bricolage','Строителство', 'Облицовка за външна стена'],
            ['Строителни материали', 'bricolage','Строителство', 'Сухи строителни смеси'],
            ['Строителни материали', 'bricolage','Строителство', 'Покривни материали / Груб строеж'],
            ['Стълби и скелета', 'bricolage','Строителство', 'Стълби и скелета'],
            ['ВИК и отводняване', 'bricolage','Строителство', 'Отводняване'],
            ['Текстил за дома', 'praktiker','Мебели и интериор', 'Текстил за спалня'],
            ['Текстил за дома', 'praktiker','Мебели и интериор', 'Текстил за кухня'],
            ['Мебели и обков', 'praktiker','Мебели и интериор', 'Мебели и обков'],
            ['Корнизи, щори и пердета', 'praktiker','Мебели и интериор', 'Корнизи, щори и пердета'],
            ['Тапети и облицовки', 'praktiker','Мебели и интериор', 'Тапети и фототапети'],
            ['Осветление и декорации', 'praktiker','Мебели и интериор', 'Вътрешно осветление'],
            ['Осветление и декорации', 'praktiker','Мебели и интериор', 'Декорации'],
            ['Домашни потреби и почистване', 'praktiker','Мебели и интериор', 'Изтривалки'],
            ['Текстил за дома', 'bricolage','Мебели и интериор', 'Домашен текстил'],
            ['Мебели и обков', 'bricolage','Мебели и интериор', 'Мебели'],
            ['Мебели и обков', 'bricolage','Мебели и интериор', 'Врати'],
            ['Корнизи, щори и пердета', 'bricolage','Мебели и интериор', 'Корнизи'],
            ['Корнизи, щори и пердета', 'bricolage','Мебели и интериор', 'Щори'],
            ['Тапети и облицовки', 'bricolage','Мебели и интериор', 'Тапети'],
            ['Тапети и облицовки', 'bricolage','Мебели и интериор', 'Ламперия'],
            ['Тапети и облицовки', 'bricolage','Мебели и интериор', 'Таванни плочи и профили'],
            ['Осветление и декорации', 'bricolage','Мебели и интериор', 'Интериорна декорация'],
            ['Осветление и декорации', 'bricolage','Мебели и интериор', 'Картини и рамки'],
            ['Домашни потреби и почистване', 'bricolage','Мебели и интериор', 'Домашни потреби'],
            ['Домашни потреби и почистване', 'bricolage','Мебели и интериор', 'Почистващи препарати'],
            ['Бои и покрития', 'praktiker','Бои и лакове', 'Интериорни бои'],
            ['Бои и покрития', 'praktiker','Бои и лакове', 'Фасадни бои и мазилки'],
            ['Лакове и дървени покрития', 'praktiker','Бои и лакове', 'Лакове и импрегнанти за дърво'],
            ['Грундове и разредители', 'praktiker','Бои и лакове', 'Грундове и разредители'],
            ['Инструменти и консумативи за боядисване', 'praktiker','Бои и лакове', 'Бояджийски инструменти и аксесоари'],
            ['Бои и покрития', 'bricolage','Бои и лакове', 'Бои за дърво и метал'],
            ['Бои и покрития', 'bricolage','Бои и лакове', 'Бои стени и тавани'],
            ['Бои и покрития', 'bricolage','Бои и лакове', 'Бои специално приложение'],
            ['Лакове и дървени покрития', 'bricolage','Бои и лакове', 'Лакове'],
            ['Лакове и дървени покрития', 'bricolage','Бои и лакове', 'Поддръжка на дърво'],
            ['Грундове и разредители', 'bricolage','Бои и лакове', 'Грундове, разредители'],
            ['Инструменти и консумативи за боядисване', 'bricolage','Бои и лакове', 'Консумативи и инструменти'],
            ['Градински мебели и декорация', 'praktiker','Двор и градина', 'Градински мебели'],
            ['Градински мебели и декорация', 'praktiker','Двор и градина', 'Саксии, кашпи и цветарници'],
            ['Барбекю и къмпинг', 'praktiker','Двор и градина', 'Барбекю'],
            ['Барбекю и къмпинг', 'praktiker','Двор и градина', 'Къмпинг'],
            ['Напояване и поливане', 'praktiker','Двор и градина', 'Напояване'],
            ['Напояване и поливане', 'praktiker','Двор и градина', 'Лейки и пулверизатори'],
            ['Басейни и водни съоръжения', 'praktiker','Двор и градина', 'Басейни, плажни артикули и оборудване'],
            ['Огради и парници', 'praktiker','Двор и градина', 'Мрежи и огради'],
            ['Играчки и съоръжения за деца', 'praktiker','Двор и градина', 'Играчки, люлки и пързалки'],
            ['Защита и борба с вредители', 'praktiker','Двор и градина', 'Защита от вредители'],
            ['Градински мебели и декорация', 'bricolage','Двор и градина', 'Градински мебели и декорация'],
            ['Барбекю и къмпинг', 'bricolage','Двор и градина', 'Барбекюта'],
            ['Напояване и поливане', 'bricolage','Двор и градина', 'Поливане / Напояване /'],
            ['Басейни и водни съоръжения', 'bricolage','Двор и градина', 'Басейни, джакузита'],
            ['Градински инструменти и техника', 'bricolage','Двор и градина', 'Градински инструменти /ръчни/'],
            ['Градински инструменти и техника', 'bricolage','Двор и градина', 'Градинска техника /машини/'],
            ['Огради и парници', 'bricolage','Двор и градина', 'Огради, мрежи, парници'],
            ['Защита и борба с вредители', 'bricolage','Двор и градина', 'Борба с вредители /Агроаптека, пръскачки/'],
            ['Мебели и огледала за баня', 'praktiker','Баня и плочки', 'Мебели и огледала за баня'],
            ['Санитария и керамика', 'praktiker','Баня и плочки', 'Санитарна керамика и аксесоари'],
            ['Смесители и душове', 'praktiker','Баня и плочки', 'Смесители и душове'],
            ['Душ кабини и сауни', 'praktiker','Баня и плочки', 'Душкабини и сауни'],
            ['Аксесоари за баня', 'praktiker','Баня и плочки', 'Аксесоари за баня'],
            ['Аксесоари за баня', 'praktiker','Баня и плочки', 'Вентилатори за баня и аксесоари'],
            ['Плочки и облицовки', 'praktiker','Баня и плочки', 'Плочки и лайстни'],
            ['Бойлери и ВиК', 'praktiker','Баня и плочки', 'Бойлери'],
            ['Мебели и огледала за баня', 'bricolage','Баня и плочки', 'Мебели за баня, огледала'],
            ['Санитария и керамика', 'bricolage','Баня и плочки', 'Санитария'],
            ['Санитария и керамика', 'bricolage','Баня и плочки', 'Мивки и умивалници'],
            ['Смесители и душове', 'bricolage','Баня и плочки', 'Смесители'],
            ['Смесители и душове', 'bricolage','Баня и плочки', 'Душове'],
            ['Душ кабини и сауни', 'bricolage','Баня и плочки', 'Душ кабини'],
            ['Плочки и облицовки', 'bricolage','Баня и плочки', 'Плочки'],
            ['Бойлери и ВиК', 'bricolage','Баня и плочки', 'ВиК'],
            ['Бойлери и ВиК', 'bricolage','Баня и плочки', 'Бойлери'],
            ['Външно и градинско осветление', 'praktiker','Осветление', 'Външно осветление'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Плафони'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Аплици'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Лампиони'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Спотове'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Полилеи и пендели'],
            ['Вътрешно осветление', 'bricolage','Осветление', 'Настолни лампи'],
            ['Външно и градинско осветление', 'bricolage','Осветление', 'Градинско осветление'],
            ['LED осветление и крушки', 'bricolage','Осветление', 'LED осветление'],
            ['LED осветление и крушки', 'bricolage','Осветление', 'Крушки'],
            ['Вградено осветление', 'bricolage','Осветление', 'Луни и панели за вграждане'],
            ['Мобилни телефони и устройства', 'bricolage','Електричество', 'Мобилни телефони и устройства'],
            ['Управление и защита на дома', 'bricolage','Електричество', 'Управление на дома'],
            ['Управление и защита на дома', 'bricolage','Електричество', 'Защита на дома'],
            ['Осветление и прожектори', 'bricolage','Електричество', 'Фенери, работни лампи, прожектори'],
            ['ТВ и аудио аксесоари', 'bricolage','Електричество', ''],
            ['ТВ и аудио аксесоари', 'bricolage','Електричество', 'ТВ и аудио аксесоари'],
            ['Електроматериали и свързващи устройства', 'bricolage','Електричество', 'Електроматериали'],
            ['Електроматериали и свързващи устройства', 'bricolage','Електричество', 'Ключове и контакти'],
            ['Електроматериали и свързващи устройства', 'bricolage','Електричество', 'Разклонители и удължители'],
            ['Уреди за бита', 'bricolage','Електричество', 'Уреди за бита']
        ];

        foreach ($baseSubcategoriesData as $subCategory) {
            BaseSubcategoryFactory::new()->create([
                'title' => $subCategory[0],
                'website' => $subCategory[1],
                'category' => $subCategory[2],
                'replaceSubCategory' => $subCategory[3],
            ]);
        }

        $mainCategoryRecords = [
            ['title' => 'Дом и интериор', 'slug' => 'dom-i-interior', 'img' => 'dom-i-interior.webp'],
            ['title' => 'Строителство', 'slug' => 'stroitelstvo', 'img' => 'stroitelstvo-i-remont.webp'],
            ['title' => 'Свободно време', 'slug' => 'svobodno-vreme', 'img' => 'svobodno-vreme-i-transport.webp'],
            ['title' => 'Двор и градина', 'slug' => 'dvor-i-gradina', 'img' => 'dvor-i-gradina.webp'],
            ['title' => 'Отопление и охлаждане', 'slug' => 'otoplenie-i-ohlazdane', 'img' => 'otoplenie-i-ohlazdane.webp'],
            ['title' => 'Козметика', 'slug' => 'kozmetika', 'img' => 'kozmetika.webp'],
            // Additional records without images
            ['title' => 'Здраве и красота', 'slug' => 'zdrave-i-krasota', 'img' => null],
            ['title' => 'Образование', 'slug' => 'obrazovanie', 'img' => null],
            ['title' => 'Техника', 'slug' => 'tehnika', 'img' => null],
            ['title' => 'Хранене и напитки', 'slug' => 'hrana-i-napitki', 'img' => null],
            ['title' => 'Спорт и фитнес', 'slug' => 'sport-i-fitness', 'img' => null],
            ['title' => 'Пътувания и туризъм', 'slug' => 'patuvania-i-turizam', 'img' => null],
            ['title' => 'Детски игри и забавления', 'slug' => 'detski-igri-i-zabavlenia', 'img' => null],
            ['title' => 'Изкуство и хоби', 'slug' => 'izkustvo-i-hobi', 'img' => null],
            ['title' => 'Кулинарни рецепти', 'slug' => 'kulinarni-recepti', 'img' => null],
            ['title' => 'Финанси и бизнес', 'slug' => 'finansi-i-biznes', 'img' => null],
        ];

        foreach ($mainCategoryRecords as $record) {
            MainCategoryFactory::new()->create($record);
        }

        $manager->flush();
    }
}
