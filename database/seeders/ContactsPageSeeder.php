<?php

namespace Database\Seeders;

use App\Models\ContactsPage;
use Illuminate\Database\Seeder;

class ContactsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = ContactsPage::current();

        if ($page->heading) {
            return;
        }

        $page->update([
            'heading' => [
                'uk' => 'Форма Зворотнього Зв’язку',
                'en' => 'Feedback form',
            ],
            'name_label' => [
                'uk' => "Ім'я Прізвище",
                'en' => 'First Name Last Name',
            ],
            'phone_label' => [
                'uk' => 'Номер телефону',
                'en' => 'Phone number',
            ],
            'email_label' => [
                'uk' => 'E-мейл',
                'en' => 'E-mail',
            ],
            'message_label' => [
                'uk' => 'Повідомлення',
                'en' => 'Message',
            ],
            'send_label' => [
                'uk' => 'Надіслати',
                'en' => 'Send',
            ],
            'success_message' => [
                'uk' => "Ми зв'яжемось із вами найближчим часом ;)",
                'en' => 'We will contact you soon ;)',
            ],
            'error_message' => [
                'uk' => 'Трапилась помилка, спробуйте надіслати запит пізніше',
                'en' => 'An error occurred, please try to submit later',
            ],
            'send_again_label' => [
                'uk' => 'Відправити форму повторно',
                'en' => 'Resubmit form',
            ],
            'map_enabled' => false,
            'map_lat' => 50.42194783,
            'map_lng' => 30.55326286,
            'map_zoom' => 16,
            'meta_title' => [
                'uk' => 'KAN | Контакти',
                'en' => 'KAN | Contacts',
            ],
            'meta_description' => [
                'uk' => 'Зв’яжіться з KAN Development — форма зворотного зв’язку.',
                'en' => 'Contact KAN Development — feedback form.',
            ],
        ]);
    }
}
