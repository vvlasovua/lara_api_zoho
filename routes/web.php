<?php

use App\Http\Controllers\Web\ApiZohoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');

    $zoho = new ApiZohoController();

    //$res = $zoho->generate_refresh_token($zoho->get_token_url());
    //$res = $zoho->generate_access_token($zoho->get_access_token_url());
    //$access_token = $zoho->generate_access_token($zoho->get_access_token_url());
    //dump($zoho->get_list_records('Deals'));
    //dump($zoho->get_record_by_id('Deals', '4881000000000339001'));
    //dump($zoho->get_user_by_id('4881000000000307001'));

    //формируем массив для создания сделки
    /*$post_data = [
        "data" => [
            [
                "Owner" => [
                    "id" => "4881000000000307001"
                ],
                "Closing_Date" => "2021-05-16",
                "Deal_Name" => "Тестовая сделка",
                "Expected_Revenue" => 65500,
                "Stage" => "Negotiation/Review",
                "Account_Name" => [
                    "id" => "4881000000000332100"
                ],
                "Amount" => 65500,
                "Probability" => 89
            ]
        ]
    ];*/

    //получили ответ от созданной заявки к примеру забрали ID (4881000000000339001)
    //dump($zoho->create_record('Deals', $post_data));

    //формируем задачу для модуля Deals

    /*$post_data_task = [
        "data" => [
            [
                "Owner" => [
                    "id" => "4881000000000307001"
                ],
                "Who_Id" => [
                    "id" => "4881000000000332196" // ID contacts
                ],
                "What_Id" => [
                    "id" => "4881000000000339001" //ID deals
                ],
                "Status" => "In Progress",
                "Send_Notification_Email" => true,
                "Description" => "Описание заявки",
                "Due_Date" => "2021-05-14",
                "Priority" => "Low",
                "send_notification" => true,
                "Subject" => "Позвонить по тестовой заявке",
                "Remind_At" => [
                    "ALARM" => "FREQ=NONE;ACTION=EMAIL;TRIGGER=DATE-TIME:2021-05-13T17:09:00+05:30"
                ],
                "\$se_module" => "Deals",
            ]
        ]
    ];*/

    //dd($zoho->create_record('Tasks', $post_data_task));




});
