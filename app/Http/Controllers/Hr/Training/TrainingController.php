<?php

namespace App\Http\Controllers\Hr\Training;

use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Training;
use App\Models\Hr\TrainingNames;
use Carbon\Carbon;
use DB, Validator, DataTables, ACL;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
	// Show Add Training Form
    public function showForm()
    {
        $getData = array(
    0 => array(
        'NAME' => 'Mr. Ayub Ali',
        'PID' => '04E0019A',
        'SHEET_AMT' => '216,990',
        'BANK_PAY_AMT' => '100,000',
        'TDS' => '2,000',
        'PAYABLE' => '98,000',
        'AC_NO' => '115.103.7672',
        'SHEET_TYPE' => 'CEILHO'
    ),
    1 => array(
        'NAME' => 'Dipankar Nandy',
        'PID' => '17E0089C',
        'SHEET_AMT' => '70,401',
        'BANK_PAY_AMT' => '55,000',
        'TDS' => '417',
        'PAYABLE' => '54,583',
        'AC_NO' => '115.103.325098',
        'SHEET_TYPE' => 'CEILHO'
    ),
    2 => array(
        'NAME' => 'Md.Khaled Hossain',
        'PID' => '18M0332C',
        'SHEET_AMT' => '72,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.167718',
        'SHEET_TYPE' => 'CEILHO'
    ),
    3 => array(
        'NAME' => 'Mr. Mostafa Jamal',
        'PID' => '97K0709D',
        'SHEET_AMT' => '71,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.126835',
        'SHEET_TYPE' => 'CEILHO'
    ),
    4 => array(
        'NAME' => 'Md. Insanur Rahman',
        'PID' => '10E0713D',
        'SHEET_AMT' => '47,490',
        'BANK_PAY_AMT' => '47,490',
        'TDS' => '417',
        'PAYABLE' => '47,073',
        'AC_NO' => '115.103.257500',
        'SHEET_TYPE' => 'CEILHO'
    ),
    5 => array(
        'NAME' => 'Md. Motiur Rahman',
        'PID' => '12H0719D',
        'SHEET_AMT' => '49,590',
        'BANK_PAY_AMT' => '32,000',
        'TDS' => '-',
        'PAYABLE' => '32,000',
        'AC_NO' => '115.103.257493',
        'SHEET_TYPE' => 'CEILHO'
    ),
    6 => array(
        'NAME' => 'Mr. Pradeep Day',
        'PID' => '98A0720D',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '102.101.331299',
        'SHEET_TYPE' => 'CEILHO'
    ),
    7 => array(
        'NAME' => 'Mr. Md. Mustafizur Rahman',
        'PID' => '12L0726D',
        'SHEET_AMT' => '88,990',
        'BANK_PAY_AMT' => '60,000',
        'TDS' => '500',
        'PAYABLE' => '59,500',
        'AC_NO' => '115.103.141008',
        'SHEET_TYPE' => 'CEILHO'
    ),
    8 => array(
        'NAME' => 'Md. Rafiqul Islam',
        'PID' => '15M0736D',
        'SHEET_AMT' => '36,490',
        'BANK_PAY_AMT' => '34,000',
        'TDS' => '-',
        'PAYABLE' => '34,000',
        'AC_NO' => '115.103.271818',
        'SHEET_TYPE' => 'CEILHO'
    ),
    9 => array(
        'NAME' => 'Kamrul Islam Chowdhury',
        'PID' => '18B0746D',
        'SHEET_AMT' => '18,490',
        'BANK_PAY_AMT' => '18,490',
        'TDS' => '-',
        'PAYABLE' => '18,490',
        'AC_NO' => '115.103.339716',
        'SHEET_TYPE' => 'CEILHO'
    ),
    10 => array(
        'NAME' => 'Mr. Shaikh Shahnewaz Kaiser',
        'PID' => '10H0748D',
        'SHEET_AMT' => '87,990',
        'BANK_PAY_AMT' => '50,000',
        'TDS' => '900',
        'PAYABLE' => '49,100',
        'AC_NO' => '115.103.138495',
        'SHEET_TYPE' => 'CEILHO'
    ),
    11 => array(
        'NAME' => 'Mr. Kazi Sabbir Mahmud',
        'PID' => '09C0752D',
        'SHEET_AMT' => '94,990',
        'BANK_PAY_AMT' => '68,000',
        'TDS' => '1,000',
        'PAYABLE' => '67,000',
        'AC_NO' => '115.103.74867',
        'SHEET_TYPE' => 'CEILHO'
    ),
    12 => array(
        'NAME' => 'Mr. Majibur Rahman',
        'PID' => '12J0753D',
        'SHEET_AMT' => '39,990',
        'BANK_PAY_AMT' => '39,990',
        'TDS' => '-',
        'PAYABLE' => '39,990',
        'AC_NO' => '115.103.164144',
        'SHEET_TYPE' => 'CEILHO'
    ),
    13 => array(
        'NAME' => 'Mr. Arif Ashab',
        'PID' => '13D0762D',
        'SHEET_AMT' => '57,990',
        'BANK_PAY_AMT' => '50,000',
        'TDS' => '-',
        'PAYABLE' => '50,000',
        'AC_NO' => '115.103.154973',
        'SHEET_TYPE' => 'CEILHO'
    ),
    14 => array(
        'NAME' => 'Md. Arif Hossain',
        'PID' => '14C0763D',
        'SHEET_AMT' => '299,990',
        'BANK_PAY_AMT' => '115,000',
        'TDS' => '6,000',
        'PAYABLE' => '109,000',
        'AC_NO' => '115.103.174756',
        'SHEET_TYPE' => 'CEILHO'
    ),
    15 => array(
        'NAME' => 'Mr. Ashiqur Rahman',
        'PID' => '13F0770D',
        'SHEET_AMT' => '94,990',
        'BANK_PAY_AMT' => '50,000',
        'TDS' => '1,000',
        'PAYABLE' => '49,000',
        'AC_NO' => '115.103.183880',
        'SHEET_TYPE' => 'CEILHO'
    ),
    16 => array(
        'NAME' => 'Mr. Amdadul Haque',
        'PID' => '13L0774D',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '69,990',
        'TDS' => '-',
        'PAYABLE' => '69,990',
        'AC_NO' => '115.103.166908',
        'SHEET_TYPE' => 'CEILHO'
    ),
    17 => array(
        'NAME' => 'Md. Mahmudul Hasan Shakil',
        'PID' => '18A0808D',
        'SHEET_AMT' => '25,990',
        'BANK_PAY_AMT' => '25,990',
        'TDS' => '-',
        'PAYABLE' => '25,990',
        'AC_NO' => '115.103.343609',
        'SHEET_TYPE' => 'CEILHO'
    ),
    18 => array(
        'NAME' => 'Sheuly Akter',
        'PID' => '17K0828U',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.339651',
        'SHEET_TYPE' => 'CEILHO'
    ),
    19 => array(
        'NAME' => 'Mr. Md. Abu Zahead',
        'PID' => '06D4557J',
        'SHEET_AMT' => '79,990',
        'BANK_PAY_AMT' => '79,990',
        'TDS' => '716',
        'PAYABLE' => '79,274',
        'AC_NO' => '115.103.140997',
        'SHEET_TYPE' => 'CEILHO'
    ),
    20 => array(
        'NAME' => 'Md.Samsul Haque',
        'PID' => '97F4559J',
        'SHEET_AMT' => '34,990',
        'BANK_PAY_AMT' => '23,000',
        'TDS' => '-',
        'PAYABLE' => '23,000',
        'AC_NO' => '115.103.326779',
        'SHEET_TYPE' => 'CEILHO'
    ),
    21 => array(
        'NAME' => 'Md.Sojib',
        'PID' => '12F4738J',
        'SHEET_AMT' => '20,490',
        'BANK_PAY_AMT' => '20,490',
        'TDS' => '-',
        'PAYABLE' => '20,490',
        'AC_NO' => '115.103.325537',
        'SHEET_TYPE' => 'CEILHO'
    ),
    22 => array(
        'NAME' => 'Md. Julkar Nayen Khan',
        'PID' => '18C5287M',
        'SHEET_AMT' => '97,990',
        'BANK_PAY_AMT' => '32,000',
        'TDS' => '-',
        'PAYABLE' => '32,000',
        'AC_NO' => '115.103.358939',
        'SHEET_TYPE' => 'CEILHO'
    ),
    23 => array(
        'NAME' => 'Al Amin Molla',
        'PID' => '18C5453Q',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.338696',
        'SHEET_TYPE' => 'CEILHO'
    ),
    24 => array(
        'NAME' => 'Abul Khair',
        'PID' => '16J5496Q',
        'SHEET_AMT' => '71,990',
        'BANK_PAY_AMT' => '60,000',
        'TDS' => '500',
        'PAYABLE' => '59,500',
        'AC_NO' => '115.103.337293',
        'SHEET_TYPE' => 'CEILHO'
    ),
    25 => array(
        'NAME' => 'Zannatul Ferdous',
        'PID' => '93A0015A',
        'SHEET_AMT' => '57,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.338841',
        'SHEET_TYPE' => 'MBMHO'
    ),
    26 => array(
        'NAME' => 'Mohammad Ibn Shaleh',
        'PID' => '18A0048D',
        'SHEET_AMT' => '64,990',
        'BANK_PAY_AMT' => '45,000',
        'TDS' => '400',
        'PAYABLE' => '44,600',
        'AC_NO' => '115.103.339646',
        'SHEET_TYPE' => 'MBMHO'
    ),
    27 => array(
        'NAME' => 'Lamia Disha',
        'PID' => '18B0702D',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.358068',
        'SHEET_TYPE' => 'MBMHO'
    ),
    28 => array(
        'NAME' => 'Md. Jillur Rhoman',
        'PID' => '11G0705D',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.339758',
        'SHEET_TYPE' => 'MBMHO'
    ),
    29 => array(
        'NAME' => 'Md. Asaduzzaman',
        'PID' => '96G0714D',
        'SHEET_AMT' => '43,090',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.339950',
        'SHEET_TYPE' => 'MBMHO'
    ),
    30 => array(
        'NAME' => 'Muhammad Monwar Hossen',
        'PID' => '13A0718D',
        'SHEET_AMT' => '31,990',
        'BANK_PAY_AMT' => '31,990',
        'TDS' => '-',
        'PAYABLE' => '31,990',
        'AC_NO' => '115.103.339945',
        'SHEET_TYPE' => 'MBMHO'
    ),
    31 => array(
        'NAME' => 'Ahsan Habib',
        'PID' => '98B0721D',
        'SHEET_AMT' => '38,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.339672',
        'SHEET_TYPE' => 'MBMHO'
    ),
    32 => array(
        'NAME' => 'Nazrul Islam',
        'PID' => '12C0730D',
        'SHEET_AMT' => '43,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.338878',
        'SHEET_TYPE' => 'MBMHO'
    ),
    33 => array(
        'NAME' => 'Md. Hazrat Ali',
        'PID' => '16E0740D',
        'SHEET_AMT' => '13,490',
        'BANK_PAY_AMT' => '13,490',
        'TDS' => '-',
        'PAYABLE' => '13,490',
        'AC_NO' => '115.103.364335',
        'SHEET_TYPE' => 'MBMHO'
    ),
    34 => array(
        'NAME' => 'Md. Abu Raihan Rocky',
        'PID' => '16L0754D',
        'SHEET_AMT' => '30,990',
        'BANK_PAY_AMT' => '30,990',
        'TDS' => '-',
        'PAYABLE' => '30,990',
        'AC_NO' => '115.103.338680',
        'SHEET_TYPE' => 'MBMHO'
    ),
    35 => array(
        'NAME' => 'Md. Shafiqul Islam',
        'PID' => '16M0755D',
        'SHEET_AMT' => '27,490',
        'BANK_PAY_AMT' => '27,490',
        'TDS' => '-',
        'PAYABLE' => '27,490',
        'AC_NO' => '115.103.338766',
        'SHEET_TYPE' => 'MBMHO'
    ),
    36 => array(
        'NAME' => 'Md. Amir Khusru',
        'PID' => '17C0757D',
        'SHEET_AMT' => '27,990',
        'BANK_PAY_AMT' => '27,990',
        'TDS' => '-',
        'PAYABLE' => '27,990',
        'AC_NO' => '115.103.305460',
        'SHEET_TYPE' => 'MBMHO'
    ),
    37 => array(
        'NAME' => 'Mr. Siam Hossain',
        'PID' => '11A0758D',
        'SHEET_AMT' => '47,490',
        'BANK_PAY_AMT' => '25,000',
        'TDS' => '-',
        'PAYABLE' => '25,000',
        'AC_NO' => '115.103.257472',
        'SHEET_TYPE' => 'MBMHO'
    ),
    38 => array(
        'NAME' => 'Md. Mobarak Ali',
        'PID' => '11B0759D',
        'SHEET_AMT' => '29,490',
        'BANK_PAY_AMT' => '29,490',
        'TDS' => '-',
        'PAYABLE' => '29,490',
        'AC_NO' => '115.103.338675',
        'SHEET_TYPE' => 'MBMHO'
    ),
    39 => array(
        'NAME' => 'Quazi Ajmal Hossain Chowdhury',
        'PID' => '17G0761D',
        'SHEET_AMT' => '22,490',
        'BANK_PAY_AMT' => '22,490',
        'TDS' => '-',
        'PAYABLE' => '22,490',
        'AC_NO' => '115.103.364350',
        'SHEET_TYPE' => 'MBMHO'
    ),
    40 => array(
        'NAME' => 'Mr.Abul Kalam Azad',
        'PID' => '11D0764D',
        'SHEET_AMT' => '43,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.169226',
        'SHEET_TYPE' => 'MBMHO'
    ),
    41 => array(
        'NAME' => 'Md. Masud Rana',
        'PID' => '17M0767D',
        'SHEET_AMT' => '27,592',
        'BANK_PAY_AMT' => '27,592',
        'TDS' => '-',
        'PAYABLE' => '27,592',
        'AC_NO' => '115.103.339693',
        'SHEET_TYPE' => 'MBMHO'
    ),
    42 => array(
        'NAME' => 'Md. Razu Ahmed',
        'PID' => '13F0769D',
        'SHEET_AMT' => '34,990',
        'BANK_PAY_AMT' => '34,990',
        'TDS' => '-',
        'PAYABLE' => '34,990',
        'AC_NO' => '115.103.338737',
        'SHEET_TYPE' => 'MBMHO'
    ),
    43 => array(
        'NAME' => 'Sayed Mohiuddin',
        'PID' => '17M0771D',
        'SHEET_AMT' => '30,490',
        'BANK_PAY_AMT' => '30,490',
        'TDS' => '-',
        'PAYABLE' => '30,490',
        'AC_NO' => '117.101.264083',
        'SHEET_TYPE' => 'MBMHO'
    ),
    44 => array(
        'NAME' => 'Md. Aminul Islam',
        'PID' => '13L0775D',
        'SHEET_AMT' => '73,990',
        'BANK_PAY_AMT' => '65,000',
        'TDS' => '500',
        'PAYABLE' => '64,500',
        'AC_NO' => '115.103.253634',
        'SHEET_TYPE' => 'MBMHO'
    ),
    45 => array(
        'NAME' => 'Md. Nawaz Mahmud Himal',
        'PID' => '14M0780D',
        'SHEET_AMT' => '15,990',
        'BANK_PAY_AMT' => '15,990',
        'TDS' => '-',
        'PAYABLE' => '15,990',
        'AC_NO' => '115.103.340126',
        'SHEET_TYPE' => 'MBMHO'
    ),
    46 => array(
        'NAME' => 'Mr. Manik Lal Saha',
        'PID' => '15L0787D',
        'SHEET_AMT' => '46,990',
        'BANK_PAY_AMT' => '34,000',
        'TDS' => '-',
        'PAYABLE' => '34,000',
        'AC_NO' => '115.103.257467',
        'SHEET_TYPE' => 'MBMHO'
    ),
    47 => array(
        'NAME' => 'Ms. Lamia Muzammel Zenia',
        'PID' => '18K0789D',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '213.105.4116',
        'SHEET_TYPE' => 'MBMHO'
    ),
    48 => array(
        'NAME' => 'Mohammad Shakhawat Hossain',
        'PID' => '19A0792D',
        'SHEET_AMT' => '99,990',
        'BANK_PAY_AMT' => '99,990',
        'TDS' => '1,000',
        'PAYABLE' => '98,990',
        'AC_NO' => '115.103.383108',
        'SHEET_TYPE' => 'MBMHO'
    ),
    49 => array(
        'NAME' => 'Mr. Al-amin Huda',
        'PID' => '19B0795D',
        'SHEET_AMT' => '47,990',
        'BANK_PAY_AMT' => '35,000',
        'TDS' => '-',
        'PAYABLE' => '35,000',
        'AC_NO' => '114.103.298919',
        'SHEET_TYPE' => 'MBMHO'
    ),
    50 => array(
        'NAME' => 'A.S.M.Sayem Pial',
        'PID' => '19D0797D',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.464477',
        'SHEET_TYPE' => 'MBMHO'
    ),
    51 => array(
        'NAME' => 'Md. Moniruzzaman',
        'PID' => '19D0798D',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '108.103.117409',
        'SHEET_TYPE' => 'MBMHO'
    ),
    52 => array(
        'NAME' => 'Md. Asaduzzaman',
        'PID' => '19E0799D',
        'SHEET_AMT' => '27,990',
        'BANK_PAY_AMT' => '27,990',
        'TDS' => '-',
        'PAYABLE' => '27,990',
        'AC_NO' => '115.103.420748',
        'SHEET_TYPE' => 'MBMHO'
    ),
    53 => array(
        'NAME' => 'Mr. Ashikuzzaman Robin',
        'PID' => '19G0800D',
        'SHEET_AMT' => '45,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.151.0305366',
        'SHEET_TYPE' => 'MBMHO'
    ),
    54 => array(
        'NAME' => 'Mr. Baten Sarder',
        'PID' => '19H0802D',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.151.0317743',
        'SHEET_TYPE' => 'MBMHO'
    ),
    55 => array(
        'NAME' => 'Md. Towhidul Islam',
        'PID' => '19J0803D',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.429299',
        'SHEET_TYPE' => 'MBMHO'
    ),
    56 => array(
        'NAME' => 'Mr. Kamal Hossen',
        'PID' => '19J0804D',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.429278',
        'SHEET_TYPE' => 'MBMHO'
    ),
    57 => array(
        'NAME' => 'Md. Mahmudul Hasan',
        'PID' => '19J0805D',
        'SHEET_AMT' => '19,990',
        'BANK_PAY_AMT' => '19,990',
        'TDS' => '-',
        'PAYABLE' => '19,990',
        'AC_NO' => '115.103.429262',
        'SHEET_TYPE' => 'MBMHO'
    ),
    58 => array(
        'NAME' => 'Sayed Abdulla Al Bashar',
        'PID' => '19J0807D',
        'SHEET_AMT' => '59,990',
        'BANK_PAY_AMT' => '59,990',
        'TDS' => '850',
        'PAYABLE' => '59,140',
        'AC_NO' => '115.103.464253',
        'SHEET_TYPE' => 'MBMHO'
    ),
    59 => array(
        'NAME' => 'Md. Towfiqur Rahman',
        'PID' => '19L0809D',
        'SHEET_AMT' => '114,990',
        'BANK_PAY_AMT' => '80,000',
        'TDS' => '715',
        'PAYABLE' => '79,285',
        'AC_NO' => '115.103.429332',
        'SHEET_TYPE' => 'MBMHO'
    ),
    60 => array(
        'NAME' => 'Somiron Banik',
        'PID' => '20A0815D',
        'SHEET_AMT' => '89,990',
        'BANK_PAY_AMT' => '89,990',
        'TDS' => '1,000',
        'PAYABLE' => '88,990',
        'AC_NO' => '115.103.438097',
        'SHEET_TYPE' => 'MBMHO'
    ),
    61 => array(
        'NAME' => 'Md. Abdur Rahim',
        'PID' => '20B0817D',
        'SHEET_AMT' => '41,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.438081',
        'SHEET_TYPE' => 'MBMHO'
    ),
    62 => array(
        'NAME' => 'Md.Rakibul Islam',
        'PID' => '20F0819D',
        'SHEET_AMT' => '34,990',
        'BANK_PAY_AMT' => '34,990',
        'TDS' => '-',
        'PAYABLE' => '34,990',
        'AC_NO' => '258.151.100812',
        'SHEET_TYPE' => 'MBMHO'
    ),
    63 => array(
        'NAME' => 'Md. Shamim Ahmed',
        'PID' => '00M4763J',
        'SHEET_AMT' => '29,990',
        'BANK_PAY_AMT' => '29,990',
        'TDS' => '-',
        'PAYABLE' => '29,990',
        'AC_NO' => '115.103.207814',
        'SHEET_TYPE' => 'MBMHO'
    ),
    64 => array(
        'NAME' => 'Shri Sudhan Chandra',
        'PID' => '00M4767J',
        'SHEET_AMT' => '29,190',
        'BANK_PAY_AMT' => '29,190',
        'TDS' => '-',
        'PAYABLE' => '29,190',
        'AC_NO' => '115.103.339700',
        'SHEET_TYPE' => 'MBMHO'
    ),
    65 => array(
        'NAME' => 'Mr. Abu Bakkar Siddiq',
        'PID' => '19D5504Q',
        'SHEET_AMT' => '26,990',
        'BANK_PAY_AMT' => '26,990',
        'TDS' => '-',
        'PAYABLE' => '26,990',
        'AC_NO' => '178.151.0113110',
        'SHEET_TYPE' => 'MBMHO'
    ),
    66 => array(
        'NAME' => 'Mr. Iqbal Ahmed Joy',
        'PID' => '18F5734K',
        'SHEET_AMT' => '10,990',
        'BANK_PAY_AMT' => '10,990',
        'TDS' => '-',
        'PAYABLE' => '10,990',
        'AC_NO' => '115.103.383115',
        'SHEET_TYPE' => 'MBMHO'
    ),
    67 => array(
        'NAME' => 'Md. Israil Khan',
        'PID' => '19J5746K',
        'SHEET_AMT' => '10,863',
        'BANK_PAY_AMT' => '10,863',
        'TDS' => '-',
        'PAYABLE' => '10,863',
        'AC_NO' => '115.103.429257',
        'SHEET_TYPE' => 'MBMHO'
    ),
    68 => array(
        'NAME' => 'Md. Solaiman',
        'PID' => '00H6991G',
        'SHEET_AMT' => '26,040',
        'BANK_PAY_AMT' => '26,040',
        'TDS' => '-',
        'PAYABLE' => '26,040',
        'AC_NO' => '115.103.338649',
        'SHEET_TYPE' => 'MBMHO'
    ),
    69 => array(
        'NAME' => 'Md. Zohirul Islam (Milon)',
        'PID' => '08J9720L',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.364319',
        'SHEET_TYPE' => 'MBMHO'
    ),
    70 => array(
        'NAME' => 'Mr. Mir Abdul Kader',
        'PID' => '88F0009A',
        'SHEET_AMT' => '86,990',
        'BANK_PAY_AMT' => '24,000',
        'TDS' => '-',
        'PAYABLE' => '24,000',
        'AC_NO' => '115.103.130198',
        'SHEET_TYPE' => 'MBMSP'
    ),
    71 => array(
        'NAME' => 'Mr. Aktaruzzaman',
        'PID' => '92A0014A',
        'SHEET_AMT' => '214,990',
        'BANK_PAY_AMT' => '80,000',
        'TDS' => '850',
        'PAYABLE' => '79,150',
        'AC_NO' => '115.103.7658',
        'SHEET_TYPE' => 'MBMSP'
    ),
    72 => array(
        'NAME' => 'G.M. Shohrab Hossain',
        'PID' => '19E0110B',
        'SHEET_AMT' => '154,990',
        'BANK_PAY_AMT' => '100,000',
        'TDS' => '1,137',
        'PAYABLE' => '98,863',
        'AC_NO' => '115.103.429348',
        'SHEET_TYPE' => 'MBMSP'
    ),
    73 => array(
        'NAME' => 'M.H.M. Twheed',
        'PID' => '19G0111B',
        'SHEET_AMT' => '40,990',
        'BANK_PAY_AMT' => '40,990',
        'TDS' => '-',
        'PAYABLE' => '40,990',
        'AC_NO' => '106.103.110150',
        'SHEET_TYPE' => 'MBMSP'
    ),
    74 => array(
        'NAME' => 'Mahmuda Jesmin Ruma',
        'PID' => '19J0113B',
        'SHEET_AMT' => '27,990',
        'BANK_PAY_AMT' => '27,990',
        'TDS' => '-',
        'PAYABLE' => '27,990',
        'AC_NO' => '115.103.438076',
        'SHEET_TYPE' => 'MBMSP'
    ),
    75 => array(
        'NAME' => 'Mst. Amena Khatun',
        'PID' => '11B0116B',
        'SHEET_AMT' => '28,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.335808',
        'SHEET_TYPE' => 'MBMSP'
    ),
    76 => array(
        'NAME' => 'Md. Khaled Al-Mamun',
        'PID' => '11J0117B',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '50,000',
        'TDS' => '417',
        'PAYABLE' => '49,583',
        'AC_NO' => '115.103.185099',
        'SHEET_TYPE' => 'MBMSP'
    ),
    77 => array(
        'NAME' => 'Rahat Sultana',
        'PID' => '19D0119B',
        'SHEET_AMT' => '22,490',
        'BANK_PAY_AMT' => '22,490',
        'TDS' => '-',
        'PAYABLE' => '22,490',
        'AC_NO' => '115.103.408921',
        'SHEET_TYPE' => 'MBMSP'
    ),
    78 => array(
        'NAME' => 'Shakila Ferdaush',
        'PID' => '07B0120B',
        'SHEET_AMT' => '38,990',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.325787',
        'SHEET_TYPE' => 'MBMSP'
    ),
    79 => array(
        'NAME' => 'Md.Imran Hossain',
        'PID' => '19M0121B',
        'SHEET_AMT' => '25,990',
        'BANK_PAY_AMT' => '25,990',
        'TDS' => '-',
        'PAYABLE' => '25,990',
        'AC_NO' => '115.103.460532',
        'SHEET_TYPE' => 'MBMSP'
    ),
    80 => array(
        'NAME' => 'Md. Samsul Haque',
        'PID' => '07M0122B',
        'SHEET_AMT' => '38,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.196005',
        'SHEET_TYPE' => 'MBMSP'
    ),
    81 => array(
        'NAME' => 'Md. Delwar Tanvir Uzzal',
        'PID' => '09E0123B',
        'SHEET_AMT' => '33,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.320637',
        'SHEET_TYPE' => 'MBMSP'
    ),
    82 => array(
        'NAME' => 'Tanvir Hossain',
        'PID' => '20A0124B',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.459699',
        'SHEET_TYPE' => 'MBMSP'
    ),
    83 => array(
        'NAME' => 'Mst. Salina Begum',
        'PID' => '12D0127B',
        'SHEET_AMT' => '44,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.325771',
        'SHEET_TYPE' => 'MBMSP'
    ),
    84 => array(
        'NAME' => 'Md. Abull Fayez',
        'PID' => '14J0132B',
        'SHEET_AMT' => '49,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.271780',
        'SHEET_TYPE' => 'MBMSP'
    ),
    85 => array(
        'NAME' => 'Mr. Younus Ali',
        'PID' => '90A0205C',
        'SHEET_AMT' => '194,990',
        'BANK_PAY_AMT' => '80,000',
        'TDS' => '1,300',
        'PAYABLE' => '78,700',
        'AC_NO' => '115.103.7660',
        'SHEET_TYPE' => 'MBMSP'
    ),
    86 => array(
        'NAME' => 'Mrs. Nurjahan',
        'PID' => '89E0223C',
        'SHEET_AMT' => '85,980',
        'BANK_PAY_AMT' => '35,000',
        'TDS' => '417',
        'PAYABLE' => '34,583',
        'AC_NO' => '115.103.251869',
        'SHEET_TYPE' => 'MBMSP'
    ),
    87 => array(
        'NAME' => 'Mr. Abul Basher',
        'PID' => '92D0226C',
        'SHEET_AMT' => '63,990',
        'BANK_PAY_AMT' => '19,000',
        'TDS' => '-',
        'PAYABLE' => '19,000',
        'AC_NO' => '115.103.127213',
        'SHEET_TYPE' => 'MBMSP'
    ),
    88 => array(
        'NAME' => 'Mr. Md. Sarwar Hossain Gazi',
        'PID' => '92D0228C',
        'SHEET_AMT' => '84,990',
        'BANK_PAY_AMT' => '25,000',
        'TDS' => '-',
        'PAYABLE' => '25,000',
        'AC_NO' => '115.103.17905',
        'SHEET_TYPE' => 'MBMSP'
    ),
    89 => array(
        'NAME' => 'Md. Chan Mia',
        'PID' => '89H0229C',
        'SHEET_AMT' => '40,990',
        'BANK_PAY_AMT' => '34,990',
        'TDS' => '417',
        'PAYABLE' => '34,573',
        'AC_NO' => '115.103.188748',
        'SHEET_TYPE' => 'MBMSP'
    ),
    90 => array(
        'NAME' => 'Mr. Md. Kamrul Islam',
        'PID' => '93K0235C',
        'SHEET_AMT' => '111,990',
        'BANK_PAY_AMT' => '50,000',
        'TDS' => '500',
        'PAYABLE' => '49,500',
        'AC_NO' => '115.103.17929',
        'SHEET_TYPE' => 'MBMSP'
    ),
    91 => array(
        'NAME' => 'Mr. Md. Abu Taleb',
        'PID' => '90L0239C',
        'SHEET_AMT' => '97,180',
        'BANK_PAY_AMT' => '27,500',
        'TDS' => '-',
        'PAYABLE' => '27,500',
        'AC_NO' => '115.103.17884',
        'SHEET_TYPE' => 'MBMSP'
    ),
    92 => array(
        'NAME' => 'Md. Samiul Alim',
        'PID' => '19M0242C',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '69,990',
        'TDS' => '715',
        'PAYABLE' => '69,275',
        'AC_NO' => '128.103.0160910',
        'SHEET_TYPE' => 'MBMSP'
    ),
    93 => array(
        'NAME' => 'Md.Bellal Hossain',
        'PID' => '89K0254C',
        'SHEET_AMT' => '34,990',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.326235',
        'SHEET_TYPE' => 'MBMSP'
    ),
    94 => array(
        'NAME' => 'Rubel',
        'PID' => '11J0255C',
        'SHEET_AMT' => '34,490',
        'BANK_PAY_AMT' => '31,000',
        'TDS' => '-',
        'PAYABLE' => '31,000',
        'AC_NO' => '115.103.321153',
        'SHEET_TYPE' => 'MBMSP'
    ),
    95 => array(
        'NAME' => 'Md.Jahangir Alam',
        'PID' => '89M0258C',
        'SHEET_AMT' => '31,490',
        'BANK_PAY_AMT' => '29,500',
        'TDS' => '-',
        'PAYABLE' => '29,500',
        'AC_NO' => '115.103.325563',
        'SHEET_TYPE' => 'MBMSP'
    ),
    96 => array(
        'NAME' => 'Md. Jalal Uddin',
        'PID' => '08F0262C',
        'SHEET_AMT' => '33,247',
        'BANK_PAY_AMT' => '31,990',
        'TDS' => '-',
        'PAYABLE' => '31,990',
        'AC_NO' => '115.103.320530',
        'SHEET_TYPE' => 'MBMSP'
    ),
    97 => array(
        'NAME' => 'Noor-E-Moontaha',
        'PID' => '18D0294C',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.340061',
        'SHEET_TYPE' => 'MBMSP'
    ),
    98 => array(
        'NAME' => 'Mr. Fazlul Haque',
        'PID' => '91F0302C',
        'SHEET_AMT' => '54,990',
        'BANK_PAY_AMT' => '24,000',
        'TDS' => '-',
        'PAYABLE' => '24,000',
        'AC_NO' => '115.103.127185',
        'SHEET_TYPE' => 'MBMSP'
    ),
    99 => array(
        'NAME' => 'Fatema',
        'PID' => '18D0304C',
        'SHEET_AMT' => '16,490',
        'BANK_PAY_AMT' => '16,490',
        'TDS' => '-',
        'PAYABLE' => '16,490',
        'AC_NO' => '115.103.339896',
        'SHEET_TYPE' => 'MBMSP'
    ),
    100 => array(
        'NAME' => 'Mr. Samim Hossain',
        'PID' => '18J0326C',
        'SHEET_AMT' => '66,990',
        'BANK_PAY_AMT' => '35,000',
        'TDS' => '417',
        'PAYABLE' => '34,583',
        'AC_NO' => '227.103.96179',
        'SHEET_TYPE' => 'MBMSP'
    ),
    101 => array(
        'NAME' => 'Sk.Nazmus Sadat',
        'PID' => '18M0338C',
        'SHEET_AMT' => '49,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.388468',
        'SHEET_TYPE' => 'MBMSP'
    ),
    102 => array(
        'NAME' => 'Mohammed Saifuddin Bhuiyan',
        'PID' => '18C0350C',
        'SHEET_AMT' => '264,990',
        'BANK_PAY_AMT' => '150,000',
        'TDS' => '7,010',
        'PAYABLE' => '142,990',
        'AC_NO' => '115.103.232298',
        'SHEET_TYPE' => 'MBMSP'
    ),
    103 => array(
        'NAME' => 'SHAFIKUL ISLAM',
        'PID' => '19K0351C',
        'SHEET_AMT' => '114,990',
        'BANK_PAY_AMT' => '86,000',
        'TDS' => '715',
        'PAYABLE' => '85,285',
        'AC_NO' => '115.103.415468',
        'SHEET_TYPE' => 'MBMSP'
    ),
    104 => array(
        'NAME' => 'Harun Or Rashid',
        'PID' => '19K0352C',
        'SHEET_AMT' => '239,990',
        'BANK_PAY_AMT' => '150,000',
        'TDS' => '4,500',
        'PAYABLE' => '145,500',
        'AC_NO' => '115.103.438045',
        'SHEET_TYPE' => 'MBMSP'
    ),
    105 => array(
        'NAME' => 'Md.  Mostafizur Rahman',
        'PID' => '13H0354C',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.322054',
        'SHEET_TYPE' => 'MBMSP'
    ),
    106 => array(
        'NAME' => 'Md.  Rafiqul Islam Rafiq',
        'PID' => '11C0367C',
        'SHEET_AMT' => '21,590',
        'BANK_PAY_AMT' => '21,590',
        'TDS' => '-',
        'PAYABLE' => '21,590',
        'AC_NO' => '115.103.320962',
        'SHEET_TYPE' => 'MBMSP'
    ),
    107 => array(
        'NAME' => 'Md Biplob Hossain',
        'PID' => '99G0397C',
        'SHEET_AMT' => '40,490',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.326508',
        'SHEET_TYPE' => 'MBMSP'
    ),
    108 => array(
        'NAME' => 'Md.Abdur Rouf',
        'PID' => '04D0401C',
        'SHEET_AMT' => '25,990',
        'BANK_PAY_AMT' => '25,990',
        'TDS' => '-',
        'PAYABLE' => '25,990',
        'AC_NO' => '115.103.459615',
        'SHEET_TYPE' => 'MBMSP'
    ),
    109 => array(
        'NAME' => 'Atiqur Rahman',
        'PID' => '17G0420C',
        'SHEET_AMT' => '42,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.335049',
        'SHEET_TYPE' => 'MBMSP'
    ),
    110 => array(
        'NAME' => 'Md. Biplob Miah',
        'PID' => '09G0593C',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.320679',
        'SHEET_TYPE' => 'MBMSP'
    ),
    111 => array(
        'NAME' => 'Mr. Emam Hossain',
        'PID' => '09B0652W',
        'SHEET_AMT' => '82,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.127190',
        'SHEET_TYPE' => 'MBMSP'
    ),
    112 => array(
        'NAME' => 'Ms. Farhana Sultana',
        'PID' => '12G0703D',
        'SHEET_AMT' => '101,990',
        'BANK_PAY_AMT' => '32,000',
        'TDS' => '-',
        'PAYABLE' => '32,000',
        'AC_NO' => '115.103.130455',
        'SHEET_TYPE' => 'MBMSP'
    ),
    113 => array(
        'NAME' => 'Md. Asaduzzaman',
        'PID' => '13C0727D',
        'SHEET_AMT' => '54,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.335881',
        'SHEET_TYPE' => 'MBMSP'
    ),
    114 => array(
        'NAME' => 'Md. Iftakharun  Nobi',
        'PID' => '18C0747D',
        'SHEET_AMT' => '52,990',
        'BANK_PAY_AMT' => '52,990',
        'TDS' => '555',
        'PAYABLE' => '52,435',
        'AC_NO' => '115.103.335876',
        'SHEET_TYPE' => 'MBMSP'
    ),
    115 => array(
        'NAME' => 'Md. Mizanur Rahman',
        'PID' => '11B0760D',
        'SHEET_AMT' => '44,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.335860',
        'SHEET_TYPE' => 'MBMSP'
    ),
    116 => array(
        'NAME' => 'Md. Mamun Ul Islam',
        'PID' => '18D0776D',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '69,990',
        'TDS' => '650',
        'PAYABLE' => '69,340',
        'AC_NO' => '115.103.335855',
        'SHEET_TYPE' => 'MBMSP'
    ),
    117 => array(
        'NAME' => 'Md Salah Uddin',
        'PID' => '18D0777D',
        'SHEET_AMT' => '73,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.335834',
        'SHEET_TYPE' => 'MBMSP'
    ),
    118 => array(
        'NAME' => 'Md Arafat Ul Kabir Chawdhury',
        'PID' => '15H0784D',
        'SHEET_AMT' => '51,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.335841',
        'SHEET_TYPE' => 'MBMSP'
    ),
    119 => array(
        'NAME' => 'Md. Abdur Razzak',
        'PID' => '19J0806D',
        'SHEET_AMT' => '35,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '110.105.26095',
        'SHEET_TYPE' => 'MBMSP'
    ),
    120 => array(
        'NAME' => 'Md. Mahabub Alom',
        'PID' => '11A1237E',
        'SHEET_AMT' => '18,490',
        'BANK_PAY_AMT' => '18,490',
        'TDS' => '-',
        'PAYABLE' => '18,490',
        'AC_NO' => '115.103.459678',
        'SHEET_TYPE' => 'MBMSP'
    ),
    121 => array(
        'NAME' => 'Md. Ibrahim',
        'PID' => '19J1240E',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.459683',
        'SHEET_TYPE' => 'MBMSP'
    ),
    122 => array(
        'NAME' => 'Md Yusuf',
        'PID' => '94L1312E',
        'SHEET_AMT' => '37,290',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.326576',
        'SHEET_TYPE' => 'MBMSP'
    ),
    123 => array(
        'NAME' => 'Md. Shahjalal',
        'PID' => '01K1442E',
        'SHEET_AMT' => '34,990',
        'BANK_PAY_AMT' => '34,990',
        'TDS' => '-',
        'PAYABLE' => '34,990',
        'AC_NO' => '115.103.325736',
        'SHEET_TYPE' => 'MBMSP'
    ),
    124 => array(
        'NAME' => 'Mst. Asma Khatun',
        'PID' => '01K1481E',
        'SHEET_AMT' => '29,790',
        'BANK_PAY_AMT' => '29,790',
        'TDS' => '-',
        'PAYABLE' => '29,790',
        'AC_NO' => '115.103.321099',
        'SHEET_TYPE' => 'MBMSP'
    ),
    125 => array(
        'NAME' => 'Abdul Alim (Rana)',
        'PID' => '02A1510E',
        'SHEET_AMT' => '29,490',
        'BANK_PAY_AMT' => '29,490',
        'TDS' => '-',
        'PAYABLE' => '29,490',
        'AC_NO' => '115.103.320749',
        'SHEET_TYPE' => 'MBMSP'
    ),
    126 => array(
        'NAME' => 'Raka',
        'PID' => '11J1564E',
        'SHEET_AMT' => '17,290',
        'BANK_PAY_AMT' => '17,290',
        'TDS' => '-',
        'PAYABLE' => '17,290',
        'AC_NO' => '115.103.325675',
        'SHEET_TYPE' => 'MBMSP'
    ),
    127 => array(
        'NAME' => 'Md.  Sakil',
        'PID' => '11J1670E',
        'SHEET_AMT' => '18,190',
        'BANK_PAY_AMT' => '18,190',
        'TDS' => '-',
        'PAYABLE' => '18,190',
        'AC_NO' => '115.103.320957',
        'SHEET_TYPE' => 'MBMSP'
    ),
    128 => array(
        'NAME' => 'Md.Zahirul Islam',
        'PID' => '02A1695E',
        'SHEET_AMT' => '19,490',
        'BANK_PAY_AMT' => '19,490',
        'TDS' => '-',
        'PAYABLE' => '19,490',
        'AC_NO' => '115.103.326310',
        'SHEET_TYPE' => 'MBMSP'
    ),
    129 => array(
        'NAME' => 'Karnafuli',
        'PID' => '11M1752E',
        'SHEET_AMT' => '19,990',
        'BANK_PAY_AMT' => '19,990',
        'TDS' => '-',
        'PAYABLE' => '19,990',
        'AC_NO' => '115.103.327810',
        'SHEET_TYPE' => 'MBMSP'
    ),
    130 => array(
        'NAME' => 'Md.Ripon Miya',
        'PID' => '09F1759E',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.464461',
        'SHEET_TYPE' => 'MBMSP'
    ),
    131 => array(
        'NAME' => 'Rubi Akhter',
        'PID' => '11M1778E',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.464456',
        'SHEET_TYPE' => 'MBMSP'
    ),
    132 => array(
        'NAME' => 'Md.Rubel Mia',
        'PID' => '10J1859E',
        'SHEET_AMT' => '19,490',
        'BANK_PAY_AMT' => '19,490',
        'TDS' => '-',
        'PAYABLE' => '19,490',
        'AC_NO' => '115.103.460553',
        'SHEET_TYPE' => 'MBMSP'
    ),
    133 => array(
        'NAME' => 'Mst.Rabeka Sultana',
        'PID' => '03G2104E',
        'SHEET_AMT' => '29,490',
        'BANK_PAY_AMT' => '29,490',
        'TDS' => '-',
        'PAYABLE' => '29,490',
        'AC_NO' => '115.103.326560',
        'SHEET_TYPE' => 'MBMSP'
    ),
    134 => array(
        'NAME' => 'Md.Nurul Amin',
        'PID' => '08A2645F',
        'SHEET_AMT' => '17,290',
        'BANK_PAY_AMT' => '17,290',
        'TDS' => '-',
        'PAYABLE' => '17,290',
        'AC_NO' => '115.103.460574',
        'SHEET_TYPE' => 'MBMSP'
    ),
    135 => array(
        'NAME' => 'Anisul Haque Mojumder',
        'PID' => '11M3014R',
        'SHEET_AMT' => '38,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.326651',
        'SHEET_TYPE' => 'MBMSP'
    ),
    136 => array(
        'NAME' => 'Monjuyara',
        'PID' => '12L3023R',
        'SHEET_AMT' => '25,490',
        'BANK_PAY_AMT' => '25,490',
        'TDS' => '-',
        'PAYABLE' => '25,490',
        'AC_NO' => '115.103.326758',
        'SHEET_TYPE' => 'MBMSP'
    ),
    137 => array(
        'NAME' => 'Mohammad Solaiman',
        'PID' => '13B3024R',
        'SHEET_AMT' => '18,790',
        'BANK_PAY_AMT' => '18,790',
        'TDS' => '-',
        'PAYABLE' => '18,790',
        'AC_NO' => '115.103.334629',
        'SHEET_TYPE' => 'MBMSP'
    ),
    138 => array(
        'NAME' => 'Md. Nasir Uddin',
        'PID' => '97G3040R',
        'SHEET_AMT' => '52,990',
        'BANK_PAY_AMT' => '34,000',
        'TDS' => '-',
        'PAYABLE' => '34,000',
        'AC_NO' => '115.103.329081',
        'SHEET_TYPE' => 'MBMSP'
    ),
    139 => array(
        'NAME' => 'Mst. Amina Khatun',
        'PID' => '05B3093R',
        'SHEET_AMT' => '25,490',
        'BANK_PAY_AMT' => '25,490',
        'TDS' => '-',
        'PAYABLE' => '25,490',
        'AC_NO' => '115.103.321452',
        'SHEET_TYPE' => 'MBMSP'
    ),
    140 => array(
        'NAME' => 'Md.  Joynal Abedin',
        'PID' => '05A3120F',
        'SHEET_AMT' => '28,990',
        'BANK_PAY_AMT' => '28,990',
        'TDS' => '-',
        'PAYABLE' => '28,990',
        'AC_NO' => '115.103.321062',
        'SHEET_TYPE' => 'MBMSP'
    ),
    141 => array(
        'NAME' => 'Md. Emdadul Haque',
        'PID' => '97B3328F',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.325792',
        'SHEET_TYPE' => 'MBMSP'
    ),
    142 => array(
        'NAME' => 'Md.Jakir Hossain Sumon',
        'PID' => '02G3408F',
        'SHEET_AMT' => '24,590',
        'BANK_PAY_AMT' => '24,590',
        'TDS' => '-',
        'PAYABLE' => '24,590',
        'AC_NO' => '115.103.325665',
        'SHEET_TYPE' => 'MBMSP'
    ),
    143 => array(
        'NAME' => 'Nurun Nahar',
        'PID' => '98J3508F',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.326331',
        'SHEET_TYPE' => 'MBMSP'
    ),
    144 => array(
        'NAME' => 'Humaun Kabir',
        'PID' => '99D3678F',
        'SHEET_AMT' => '20,990',
        'BANK_PAY_AMT' => '20,990',
        'TDS' => '-',
        'PAYABLE' => '20,990',
        'AC_NO' => '115.103.334613',
        'SHEET_TYPE' => 'MBMSP'
    ),
    145 => array(
        'NAME' => 'Hordeb Chandra Barmon',
        'PID' => '09A4006H',
        'SHEET_AMT' => '44,990',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.326298',
        'SHEET_TYPE' => 'MBMSP'
    ),
    146 => array(
        'NAME' => 'Abdus Salam',
        'PID' => '98L4007H',
        'SHEET_AMT' => '22,390',
        'BANK_PAY_AMT' => '22,390',
        'TDS' => '-',
        'PAYABLE' => '22,390',
        'AC_NO' => '115.103.328794',
        'SHEET_TYPE' => 'MBMSP'
    ),
    147 => array(
        'NAME' => 'Md. Mozammel Haque',
        'PID' => '00J4008H',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.325750',
        'SHEET_TYPE' => 'MBMSP'
    ),
    148 => array(
        'NAME' => 'Md. Abdul Khalek',
        'PID' => '00J4023H',
        'SHEET_AMT' => '35,490',
        'BANK_PAY_AMT' => '35,490',
        'TDS' => '-',
        'PAYABLE' => '35,490',
        'AC_NO' => '115.103.338633',
        'SHEET_TYPE' => 'MBMSP'
    ),
    149 => array(
        'NAME' => 'Md. Arif Hossain',
        'PID' => '12G4033H',
        'SHEET_AMT' => '15,990',
        'BANK_PAY_AMT' => '15,990',
        'TDS' => '-',
        'PAYABLE' => '15,990',
        'AC_NO' => '115.103.334634',
        'SHEET_TYPE' => 'MBMSP'
    ),
    150 => array(
        'NAME' => 'Abu Obayda',
        'PID' => '08C4037H',
        'SHEET_AMT' => '22,190',
        'BANK_PAY_AMT' => '22,190',
        'TDS' => '-',
        'PAYABLE' => '22,190',
        'AC_NO' => '115.103.320684',
        'SHEET_TYPE' => 'MBMSP'
    ),
    151 => array(
        'NAME' => 'Md.  A K M Moniruzzaman',
        'PID' => '05H4042H',
        'SHEET_AMT' => '25,096',
        'BANK_PAY_AMT' => '25,096',
        'TDS' => '-',
        'PAYABLE' => '25,096',
        'AC_NO' => '115.103.321083',
        'SHEET_TYPE' => 'MBMSP'
    ),
    152 => array(
        'NAME' => 'Md. Kamrul Islam',
        'PID' => '09M4044H',
        'SHEET_AMT' => '21,859',
        'BANK_PAY_AMT' => '21,859',
        'TDS' => '-',
        'PAYABLE' => '21,859',
        'AC_NO' => '115.103.320907',
        'SHEET_TYPE' => 'MBMSP'
    ),
    153 => array(
        'NAME' => 'Sayeed Hossen',
        'PID' => '12G4048H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.460602',
        'SHEET_TYPE' => 'MBMSP'
    ),
    154 => array(
        'NAME' => 'Sojibe Mazumder',
        'PID' => '17A4052H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.339987',
        'SHEET_TYPE' => 'MBMSP'
    ),
    155 => array(
        'NAME' => 'Md.Nowshad Ali',
        'PID' => '05J4063H',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.460650',
        'SHEET_TYPE' => 'MBMSP'
    ),
    156 => array(
        'NAME' => 'Md.Faruk Hossain',
        'PID' => '02F4087H',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.325719',
        'SHEET_TYPE' => 'MBMSP'
    ),
    157 => array(
        'NAME' => 'Abdul Alim',
        'PID' => '10A4095H',
        'SHEET_AMT' => '19,490',
        'BANK_PAY_AMT' => '19,490',
        'TDS' => '-',
        'PAYABLE' => '19,490',
        'AC_NO' => '115.103.460726',
        'SHEET_TYPE' => 'MBMSP'
    ),
    158 => array(
        'NAME' => 'Md. Abu Sayed',
        'PID' => '97K4110H',
        'SHEET_AMT' => '40,990',
        'BANK_PAY_AMT' => '33,000',
        'TDS' => '-',
        'PAYABLE' => '33,000',
        'AC_NO' => '115.103.283917',
        'SHEET_TYPE' => 'MBMSP'
    ),
    159 => array(
        'NAME' => 'Md. Masud Rana',
        'PID' => '13G4111H',
        'SHEET_AMT' => '17,056',
        'BANK_PAY_AMT' => '17,056',
        'TDS' => '-',
        'PAYABLE' => '17,056',
        'AC_NO' => '115.103.339812',
        'SHEET_TYPE' => 'MBMSP'
    ),
    160 => array(
        'NAME' => 'Md.Ruhul Amin',
        'PID' => '06B4116H',
        'SHEET_AMT' => '25,240',
        'BANK_PAY_AMT' => '25,240',
        'TDS' => '-',
        'PAYABLE' => '25,240',
        'AC_NO' => '115.103.326157',
        'SHEET_TYPE' => 'MBMSP'
    ),
    161 => array(
        'NAME' => 'Md.  Golam Azam',
        'PID' => '94G4122H',
        'SHEET_AMT' => '25,590',
        'BANK_PAY_AMT' => '25,590',
        'TDS' => '-',
        'PAYABLE' => '25,590',
        'AC_NO' => '115.103.321265',
        'SHEET_TYPE' => 'MBMSP'
    ),
    162 => array(
        'NAME' => 'Moslem',
        'PID' => '06B4125H',
        'SHEET_AMT' => '25,190',
        'BANK_PAY_AMT' => '25,190',
        'TDS' => '-',
        'PAYABLE' => '25,190',
        'AC_NO' => '115.103.326513',
        'SHEET_TYPE' => 'MBMSP'
    ),
    163 => array(
        'NAME' => 'Md. Abdus Salam',
        'PID' => '12A4135H',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.320567',
        'SHEET_TYPE' => 'MBMSP'
    ),
    164 => array(
        'NAME' => 'Md. Akramul Haque (Abu)',
        'PID' => '94C4160H',
        'SHEET_AMT' => '42,290',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.326529',
        'SHEET_TYPE' => 'MBMSP'
    ),
    165 => array(
        'NAME' => 'Md Mongil Mia',
        'PID' => '92L4161H',
        'SHEET_AMT' => '28,990',
        'BANK_PAY_AMT' => '28,990',
        'TDS' => '-',
        'PAYABLE' => '28,990',
        'AC_NO' => '115.103.321270',
        'SHEET_TYPE' => 'MBMSP'
    ),
    166 => array(
        'NAME' => 'Md. Shahjahan',
        'PID' => '07L4165H',
        'SHEET_AMT' => '23,590',
        'BANK_PAY_AMT' => '23,590',
        'TDS' => '-',
        'PAYABLE' => '23,590',
        'AC_NO' => '115.103.326032',
        'SHEET_TYPE' => 'MBMSP'
    ),
    167 => array(
        'NAME' => 'Md. Alior Rahaman',
        'PID' => '16A4168H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.340077',
        'SHEET_TYPE' => 'MBMSP'
    ),
    168 => array(
        'NAME' => 'Md. Sohel Rana',
        'PID' => '06E4192H',
        'SHEET_AMT' => '23,990',
        'BANK_PAY_AMT' => '23,990',
        'TDS' => '-',
        'PAYABLE' => '23,990',
        'AC_NO' => '115.103.320936',
        'SHEET_TYPE' => 'MBMSP'
    ),
    169 => array(
        'NAME' => 'Kazi Mehedi Hasan',
        'PID' => '96E4195H',
        'SHEET_AMT' => '39,590',
        'BANK_PAY_AMT' => '23,600',
        'TDS' => '-',
        'PAYABLE' => '23,600',
        'AC_NO' => '115.103.326459',
        'SHEET_TYPE' => 'MBMSP'
    ),
    170 => array(
        'NAME' => 'Md.Julfikar Ali',
        'PID' => '05E4196H',
        'SHEET_AMT' => '36,990',
        'BANK_PAY_AMT' => '29,500',
        'TDS' => '-',
        'PAYABLE' => '29,500',
        'AC_NO' => '115.103.326742',
        'SHEET_TYPE' => 'MBMSP'
    ),
    171 => array(
        'NAME' => 'Mr. Gholam Sorwar Khan',
        'PID' => '90L4213H',
        'SHEET_AMT' => '75,990',
        'BANK_PAY_AMT' => '25,000',
        'TDS' => '-',
        'PAYABLE' => '25,000',
        'AC_NO' => '115.103.75109',
        'SHEET_TYPE' => 'MBMSP'
    ),
    172 => array(
        'NAME' => 'Md.Fazlar Rahman',
        'PID' => '08C4222H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.460756',
        'SHEET_TYPE' => 'MBMSP'
    ),
    173 => array(
        'NAME' => 'Tohiduzzaman',
        'PID' => '08E4225H',
        'SHEET_AMT' => '21,090',
        'BANK_PAY_AMT' => '21,090',
        'TDS' => '-',
        'PAYABLE' => '21,090',
        'AC_NO' => '115.103.326144',
        'SHEET_TYPE' => 'MBMSP'
    ),
    174 => array(
        'NAME' => 'Md.Rezaul Karim',
        'PID' => '97L4226H',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.326347',
        'SHEET_TYPE' => 'MBMSP'
    ),
    175 => array(
        'NAME' => 'Md.Nura Alam Helal',
        'PID' => '05G4239H',
        'SHEET_AMT' => '25,491',
        'BANK_PAY_AMT' => '25,491',
        'TDS' => '-',
        'PAYABLE' => '25,491',
        'AC_NO' => '115.103.325416',
        'SHEET_TYPE' => 'MBMSP'
    ),
    176 => array(
        'NAME' => 'Md. Chan Miah',
        'PID' => '05G4242H',
        'SHEET_AMT' => '22,490',
        'BANK_PAY_AMT' => '22,490',
        'TDS' => '-',
        'PAYABLE' => '22,490',
        'AC_NO' => '115.103.325430',
        'SHEET_TYPE' => 'MBMSP'
    ),
    177 => array(
        'NAME' => 'Md.  Mostafizur Rahman',
        'PID' => '99E4244H',
        'SHEET_AMT' => '22,040',
        'BANK_PAY_AMT' => '22,040',
        'TDS' => '-',
        'PAYABLE' => '22,040',
        'AC_NO' => '115.103.321036',
        'SHEET_TYPE' => 'MBMSP'
    ),
    178 => array(
        'NAME' => 'Md. Manik Hossain',
        'PID' => '03D4257H',
        'SHEET_AMT' => '20,190',
        'BANK_PAY_AMT' => '20,190',
        'TDS' => '-',
        'PAYABLE' => '20,190',
        'AC_NO' => '115.103.320920',
        'SHEET_TYPE' => 'MBMSP'
    ),
    179 => array(
        'NAME' => 'Md.Sohag Mia',
        'PID' => '15C4269H',
        'SHEET_AMT' => '16,490',
        'BANK_PAY_AMT' => '16,490',
        'TDS' => '-',
        'PAYABLE' => '16,490',
        'AC_NO' => '115.103.460782',
        'SHEET_TYPE' => 'MBMSP'
    ),
    180 => array(
        'NAME' => 'Md.  Yeaquib Ali',
        'PID' => '07B4277H',
        'SHEET_AMT' => '26,990',
        'BANK_PAY_AMT' => '26,990',
        'TDS' => '-',
        'PAYABLE' => '26,990',
        'AC_NO' => '115.103.322096',
        'SHEET_TYPE' => 'MBMSP'
    ),
    181 => array(
        'NAME' => 'Md. Anayet Mia',
        'PID' => '07B4297H',
        'SHEET_AMT' => '37,990',
        'BANK_PAY_AMT' => '37,990',
        'TDS' => '-',
        'PAYABLE' => '37,990',
        'AC_NO' => '115.103.325590',
        'SHEET_TYPE' => 'MBMSP'
    ),
    182 => array(
        'NAME' => 'Musleh Uddin',
        'PID' => '12D4299H',
        'SHEET_AMT' => '16,490',
        'BANK_PAY_AMT' => '16,490',
        'TDS' => '-',
        'PAYABLE' => '16,490',
        'AC_NO' => '115.103.460777',
        'SHEET_TYPE' => 'MBMSP'
    ),
    183 => array(
        'NAME' => 'Md. Mahenur Alam',
        'PID' => '07B4314H',
        'SHEET_AMT' => '22,290',
        'BANK_PAY_AMT' => '22,290',
        'TDS' => '-',
        'PAYABLE' => '22,290',
        'AC_NO' => '115.103.320658',
        'SHEET_TYPE' => 'MBMSP'
    ),
    184 => array(
        'NAME' => 'Md. Abdus Salam',
        'PID' => '11J4334H',
        'SHEET_AMT' => '15,690',
        'BANK_PAY_AMT' => '15,690',
        'TDS' => '-',
        'PAYABLE' => '15,690',
        'AC_NO' => '115.103.334863',
        'SHEET_TYPE' => 'MBMSP'
    ),
    185 => array(
        'NAME' => 'Rased Ahmmed',
        'PID' => '08A4357H',
        'SHEET_AMT' => '37,640',
        'BANK_PAY_AMT' => '29,140',
        'TDS' => '-',
        'PAYABLE' => '29,140',
        'AC_NO' => '115.103.326490',
        'SHEET_TYPE' => 'MBMSP'
    ),
    186 => array(
        'NAME' => 'Pojir Ali',
        'PID' => '97H4360H',
        'SHEET_AMT' => '26,490',
        'BANK_PAY_AMT' => '26,490',
        'TDS' => '-',
        'PAYABLE' => '26,490',
        'AC_NO' => '115.103.320999',
        'SHEET_TYPE' => 'MBMSP'
    ),
    187 => array(
        'NAME' => 'Md.Jasim Uddin',
        'PID' => '11L4393H',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.460686',
        'SHEET_TYPE' => 'MBMSP'
    ),
    188 => array(
        'NAME' => 'Mr.Bokul Hossain',
        'PID' => '12F4400H',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.464755',
        'SHEET_TYPE' => 'MBMSP'
    ),
    189 => array(
        'NAME' => 'Md. Khalequezzaman',
        'PID' => '98C4438H',
        'SHEET_AMT' => '49,627',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.325766',
        'SHEET_TYPE' => 'MBMSP'
    ),
    190 => array(
        'NAME' => 'Md.  Ohedur Rahaman',
        'PID' => '98D4492H',
        'SHEET_AMT' => '22,790',
        'BANK_PAY_AMT' => '22,790',
        'TDS' => '-',
        'PAYABLE' => '22,790',
        'AC_NO' => '115.103.321954',
        'SHEET_TYPE' => 'MBMSP'
    ),
    191 => array(
        'NAME' => 'Md.Sahjahan Ali',
        'PID' => '96E4506J',
        'SHEET_AMT' => '22,090',
        'BANK_PAY_AMT' => '22,090',
        'TDS' => '-',
        'PAYABLE' => '22,090',
        'AC_NO' => '115.103.326597',
        'SHEET_TYPE' => 'MBMSP'
    ),
    192 => array(
        'NAME' => 'Saiful Islam',
        'PID' => '12E4531J',
        'SHEET_AMT' => '16,065',
        'BANK_PAY_AMT' => '16,065',
        'TDS' => '-',
        'PAYABLE' => '16,065',
        'AC_NO' => '115.103.326352',
        'SHEET_TYPE' => 'MBMSP'
    ),
    193 => array(
        'NAME' => 'Md.Feruz',
        'PID' => '12E4556J',
        'SHEET_AMT' => '16,065',
        'BANK_PAY_AMT' => '16,065',
        'TDS' => '-',
        'PAYABLE' => '16,065',
        'AC_NO' => '115.103.326394',
        'SHEET_TYPE' => 'MBMSP'
    ),
    194 => array(
        'NAME' => 'Md. Farhad Talukder',
        'PID' => '89E4563J',
        'SHEET_AMT' => '27,490',
        'BANK_PAY_AMT' => '27,490',
        'TDS' => '-',
        'PAYABLE' => '27,490',
        'AC_NO' => '115.103.320690',
        'SHEET_TYPE' => 'MBMSP'
    ),
    195 => array(
        'NAME' => 'Md.Ahasan Ali',
        'PID' => '90J4568J',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.326123',
        'SHEET_TYPE' => 'MBMSP'
    ),
    196 => array(
        'NAME' => 'Sree Zhontu Dhas',
        'PID' => '12A4583J',
        'SHEET_AMT' => '17,190',
        'BANK_PAY_AMT' => '17,190',
        'TDS' => '-',
        'PAYABLE' => '17,190',
        'AC_NO' => '115.103.320593',
        'SHEET_TYPE' => 'MBMSP'
    ),
    197 => array(
        'NAME' => 'Abdul Adud Azad',
        'PID' => '99D4589J',
        'SHEET_AMT' => '27,990',
        'BANK_PAY_AMT' => '27,990',
        'TDS' => '-',
        'PAYABLE' => '27,990',
        'AC_NO' => '115.103.326693',
        'SHEET_TYPE' => 'MBMSP'
    ),
    198 => array(
        'NAME' => 'Liton Mia',
        'PID' => '92H4602J',
        'SHEET_AMT' => '28,990',
        'BANK_PAY_AMT' => '28,990',
        'TDS' => '-',
        'PAYABLE' => '28,990',
        'AC_NO' => '115.103.326373',
        'SHEET_TYPE' => 'MBMSP'
    ),
    199 => array(
        'NAME' => 'Md. Nur Alam',
        'PID' => '97G4612J',
        'SHEET_AMT' => '18,190',
        'BANK_PAY_AMT' => '18,190',
        'TDS' => '-',
        'PAYABLE' => '18,190',
        'AC_NO' => '115.103.320642',
        'SHEET_TYPE' => 'MBMSP'
    ),
    200 => array(
        'NAME' => 'Md.  Alal Hossain',
        'PID' => '93G4613J',
        'SHEET_AMT' => '29,980',
        'BANK_PAY_AMT' => '29,990',
        'TDS' => '-',
        'PAYABLE' => '29,990',
        'AC_NO' => '115.103.321314',
        'SHEET_TYPE' => 'MBMSP'
    ),
    201 => array(
        'NAME' => 'Md. Bipul Khandokar',
        'PID' => '11G4688J',
        'SHEET_AMT' => '24,490',
        'BANK_PAY_AMT' => '24,490',
        'TDS' => '-',
        'PAYABLE' => '24,490',
        'AC_NO' => '115.103.340009',
        'SHEET_TYPE' => 'MBMSP'
    ),
    202 => array(
        'NAME' => 'Mashi Ahamed Babar',
        'PID' => '97G4727J',
        'SHEET_AMT' => '26,290',
        'BANK_PAY_AMT' => '26,290',
        'TDS' => '-',
        'PAYABLE' => '26,290',
        'AC_NO' => '115.103.320663',
        'SHEET_TYPE' => 'MBMSP'
    ),
    203 => array(
        'NAME' => 'Md. Selim',
        'PID' => '00D4764J',
        'SHEET_AMT' => '35,990',
        'BANK_PAY_AMT' => '35,990',
        'TDS' => '-',
        'PAYABLE' => '35,990',
        'AC_NO' => '115.103.325607',
        'SHEET_TYPE' => 'MBMSP'
    ),
    204 => array(
        'NAME' => 'Kangkar Mandal',
        'PID' => '10F4773J',
        'SHEET_AMT' => '21,290',
        'BANK_PAY_AMT' => '21,290',
        'TDS' => '-',
        'PAYABLE' => '21,290',
        'AC_NO' => '115.103.384557',
        'SHEET_TYPE' => 'MBMSP'
    ),
    205 => array(
        'NAME' => 'Md.Kamruzzaman',
        'PID' => '12D4794J',
        'SHEET_AMT' => '21,490',
        'BANK_PAY_AMT' => '21,490',
        'TDS' => '-',
        'PAYABLE' => '21,490',
        'AC_NO' => '115.103.325425',
        'SHEET_TYPE' => 'MBMSP'
    ),
    206 => array(
        'NAME' => 'Md. Ishak',
        'PID' => '15M4830J',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.334571',
        'SHEET_TYPE' => 'MBMSP'
    ),
    207 => array(
        'NAME' => 'Md. Saiful Islam',
        'PID' => '11G4903J',
        'SHEET_AMT' => '14,790',
        'BANK_PAY_AMT' => '14,790',
        'TDS' => '-',
        'PAYABLE' => '14,790',
        'AC_NO' => '115.103.334960',
        'SHEET_TYPE' => 'MBMSP'
    ),
    208 => array(
        'NAME' => 'Md. Al Amin',
        'PID' => '12L4911J',
        'SHEET_AMT' => '15,996',
        'BANK_PAY_AMT' => '15,996',
        'TDS' => '-',
        'PAYABLE' => '15,996',
        'AC_NO' => '115.103.340110',
        'SHEET_TYPE' => 'MBMSP'
    ),
    209 => array(
        'NAME' => 'Md.Al Amin',
        'PID' => '98G4957J',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.325654',
        'SHEET_TYPE' => 'MBMSP'
    ),
    210 => array(
        'NAME' => 'Md.Monir Hossain',
        'PID' => '98G4976J',
        'SHEET_AMT' => '27,290',
        'BANK_PAY_AMT' => '27,290',
        'TDS' => '-',
        'PAYABLE' => '27,290',
        'AC_NO' => '115.103.325467',
        'SHEET_TYPE' => 'MBMSP'
    ),
    211 => array(
        'NAME' => 'Md.Nurul Islam',
        'PID' => '98G4983J',
        'SHEET_AMT' => '36,490',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.326763',
        'SHEET_TYPE' => 'MBMSP'
    ),
    212 => array(
        'NAME' => 'Md. Shahidulla',
        'PID' => '05B5027P',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.334837',
        'SHEET_TYPE' => 'MBMSP'
    ),
    213 => array(
        'NAME' => 'Mohasin Reza',
        'PID' => '06E5054P',
        'SHEET_AMT' => '36,490',
        'BANK_PAY_AMT' => '29,000',
        'TDS' => '-',
        'PAYABLE' => '29,000',
        'AC_NO' => '115.103.339784',
        'SHEET_TYPE' => 'MBMSP'
    ),
    214 => array(
        'NAME' => 'Md. Shafikul Islam',
        'PID' => '04G5072P',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.335033',
        'SHEET_TYPE' => 'MBMSP'
    ),
    215 => array(
        'NAME' => 'Badsha Alamgir',
        'PID' => '03D5077P',
        'SHEET_AMT' => '22,290',
        'BANK_PAY_AMT' => '22,290',
        'TDS' => '-',
        'PAYABLE' => '22,290',
        'AC_NO' => '115.103.321106',
        'SHEET_TYPE' => 'MBMSP'
    ),
    216 => array(
        'NAME' => 'Md.Rafiqul Islam',
        'PID' => '89L5101N',
        'SHEET_AMT' => '20,640',
        'BANK_PAY_AMT' => '20,640',
        'TDS' => '-',
        'PAYABLE' => '20,640',
        'AC_NO' => '115.103.326630',
        'SHEET_TYPE' => 'MBMSP'
    ),
    217 => array(
        'NAME' => 'Md.Khaybar Ali',
        'PID' => '93F5103N',
        'SHEET_AMT' => '16,590',
        'BANK_PAY_AMT' => '16,590',
        'TDS' => '-',
        'PAYABLE' => '16,590',
        'AC_NO' => '115.103.325542',
        'SHEET_TYPE' => 'MBMSP'
    ),
    218 => array(
        'NAME' => 'Md.  Tozammel Hoque',
        'PID' => '92E5204M',
        'SHEET_AMT' => '28,190',
        'BANK_PAY_AMT' => '28,190',
        'TDS' => '-',
        'PAYABLE' => '28,190',
        'AC_NO' => '115.103.321340',
        'SHEET_TYPE' => 'MBMSP'
    ),
    219 => array(
        'NAME' => 'Md.Dulal Hossain',
        'PID' => '19L5208M',
        'SHEET_AMT' => '14,990',
        'BANK_PAY_AMT' => '14,990',
        'TDS' => '-',
        'PAYABLE' => '14,990',
        'AC_NO' => '115.103.459795',
        'SHEET_TYPE' => 'MBMSP'
    ),
    220 => array(
        'NAME' => 'Md.Muraduzzaman Murad',
        'PID' => '19M5209M',
        'SHEET_AMT' => '29,990',
        'BANK_PAY_AMT' => '29,990',
        'TDS' => '-',
        'PAYABLE' => '29,990',
        'AC_NO' => '115.103.459600',
        'SHEET_TYPE' => 'MBMSP'
    ),
    221 => array(
        'NAME' => 'Md.  Nurul Islam',
        'PID' => '90F5212M',
        'SHEET_AMT' => '28,990',
        'BANK_PAY_AMT' => '28,990',
        'TDS' => '-',
        'PAYABLE' => '28,990',
        'AC_NO' => '115.103.321309',
        'SHEET_TYPE' => 'MBMSP'
    ),
    222 => array(
        'NAME' => 'Md.  Lutfar Rahman',
        'PID' => '01F5213M',
        'SHEET_AMT' => '25,290',
        'BANK_PAY_AMT' => '25,290',
        'TDS' => '-',
        'PAYABLE' => '25,290',
        'AC_NO' => '115.103.321132',
        'SHEET_TYPE' => 'MBMSP'
    ),
    223 => array(
        'NAME' => 'Md.Ibrahim',
        'PID' => '99B5214M',
        'SHEET_AMT' => '32,990',
        'BANK_PAY_AMT' => '32,990',
        'TDS' => '-',
        'PAYABLE' => '32,990',
        'AC_NO' => '115.103.326464',
        'SHEET_TYPE' => 'MBMSP'
    ),
    224 => array(
        'NAME' => 'Mr. Abdul Kader',
        'PID' => '97K5216M',
        'SHEET_AMT' => '46,790',
        'BANK_PAY_AMT' => '43,790',
        'TDS' => '500',
        'PAYABLE' => '43,290',
        'AC_NO' => '115.103.164165',
        'SHEET_TYPE' => 'MBMSP'
    ),
    225 => array(
        'NAME' => 'Md. Millat Hossain',
        'PID' => '04G5220M',
        'SHEET_AMT' => '38,890',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.164186',
        'SHEET_TYPE' => 'MBMSP'
    ),
    226 => array(
        'NAME' => 'Md. Jahangir Alam',
        'PID' => '18B5224M',
        'SHEET_AMT' => '30,490',
        'BANK_PAY_AMT' => '30,490',
        'TDS' => '-',
        'PAYABLE' => '30,490',
        'AC_NO' => '115.103.339966',
        'SHEET_TYPE' => 'MBMSP'
    ),
    227 => array(
        'NAME' => 'Mr. Salim Reza',
        'PID' => '10K5225M',
        'SHEET_AMT' => '33,290',
        'BANK_PAY_AMT' => '33,290',
        'TDS' => '-',
        'PAYABLE' => '33,290',
        'AC_NO' => '115.103.264234',
        'SHEET_TYPE' => 'MBMSP'
    ),
    228 => array(
        'NAME' => 'Mahabub Alam',
        'PID' => '19A5228M',
        'SHEET_AMT' => '69,990',
        'BANK_PAY_AMT' => '69,990',
        'TDS' => '-',
        'PAYABLE' => '69,990',
        'AC_NO' => '115.103.388629',
        'SHEET_TYPE' => 'MBMSP'
    ),
    229 => array(
        'NAME' => 'Mr. Kalim Sarder',
        'PID' => '19B5234M',
        'SHEET_AMT' => '32,490',
        'BANK_PAY_AMT' => '32,490',
        'TDS' => '-',
        'PAYABLE' => '32,490',
        'AC_NO' => '164.103.0067346',
        'SHEET_TYPE' => 'MBMSP'
    ),
    230 => array(
        'NAME' => 'Md. Shamsul Alam',
        'PID' => '08E5237M',
        'SHEET_AMT' => '31,990',
        'BANK_PAY_AMT' => '31,990',
        'TDS' => '-',
        'PAYABLE' => '31,990',
        'AC_NO' => '115.103.269022',
        'SHEET_TYPE' => 'MBMSP'
    ),
    231 => array(
        'NAME' => 'Habibur Rahman Khan',
        'PID' => '06D5244M',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.326716',
        'SHEET_TYPE' => 'MBMSP'
    ),
    232 => array(
        'NAME' => 'Mr. Khalil Peda',
        'PID' => '07K5251M',
        'SHEET_AMT' => '38,790',
        'BANK_PAY_AMT' => '35,000',
        'TDS' => '-',
        'PAYABLE' => '35,000',
        'AC_NO' => '115.103.164153',
        'SHEET_TYPE' => 'MBMSP'
    ),
    233 => array(
        'NAME' => 'Md. Faroque Ahmmed',
        'PID' => '07L5252M',
        'SHEET_AMT' => '23,290',
        'BANK_PAY_AMT' => '23,290',
        'TDS' => '-',
        'PAYABLE' => '23,290',
        'AC_NO' => '115.103.325948',
        'SHEET_TYPE' => 'MBMSP'
    ),
    234 => array(
        'NAME' => 'Md. Jamal Uddin',
        'PID' => '08H5256M',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.320546',
        'SHEET_TYPE' => 'MBMSP'
    ),
    235 => array(
        'NAME' => 'Md.Fakhrul Islam',
        'PID' => '19H5260M',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.459641',
        'SHEET_TYPE' => 'MBMSP'
    ),
    236 => array(
        'NAME' => 'Md.Masum',
        'PID' => '12M5261M',
        'SHEET_AMT' => '22,090',
        'BANK_PAY_AMT' => '22,090',
        'TDS' => '-',
        'PAYABLE' => '22,090',
        'AC_NO' => '115.103.326616',
        'SHEET_TYPE' => 'MBMSP'
    ),
    237 => array(
        'NAME' => 'Md. Amjad Hossain',
        'PID' => '18C5264M',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.339793',
        'SHEET_TYPE' => 'MBMSP'
    ),
    238 => array(
        'NAME' => 'Md Moksedul Islam',
        'PID' => '13C5266M',
        'SHEET_AMT' => '18,290',
        'BANK_PAY_AMT' => '18,290',
        'TDS' => '-',
        'PAYABLE' => '18,290',
        'AC_NO' => '115.103.327825',
        'SHEET_TYPE' => 'MBMSP'
    ),
    239 => array(
        'NAME' => 'Khokon Joyti Chakma',
        'PID' => '18C5267M',
        'SHEET_AMT' => '16,190',
        'BANK_PAY_AMT' => '16,190',
        'TDS' => '-',
        'PAYABLE' => '16,190',
        'AC_NO' => '115.103.339828',
        'SHEET_TYPE' => 'MBMSP'
    ),
    240 => array(
        'NAME' => 'Md. Rofikul Islam',
        'PID' => '10D5270M',
        'SHEET_AMT' => '24,790',
        'BANK_PAY_AMT' => '24,790',
        'TDS' => '-',
        'PAYABLE' => '24,790',
        'AC_NO' => '115.103.339721',
        'SHEET_TYPE' => 'MBMSP'
    ),
    241 => array(
        'NAME' => 'Md Joshim Uddin',
        'PID' => '16A5294M',
        'SHEET_AMT' => '21,490',
        'BANK_PAY_AMT' => '21,490',
        'TDS' => '-',
        'PAYABLE' => '21,490',
        'AC_NO' => '115.103.326191',
        'SHEET_TYPE' => 'MBMSP'
    ),
    242 => array(
        'NAME' => 'Md. Shahin Alam',
        'PID' => '16L5296M',
        'SHEET_AMT' => '14,990',
        'BANK_PAY_AMT' => '14,990',
        'TDS' => '-',
        'PAYABLE' => '14,990',
        'AC_NO' => '115.103.358047',
        'SHEET_TYPE' => 'MBMSP'
    ),
    243 => array(
        'NAME' => 'Md.Nurullah',
        'PID' => '11L5308L',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.326784',
        'SHEET_TYPE' => 'MBMSP'
    ),
    244 => array(
        'NAME' => 'Md. Shofiqul Islam',
        'PID' => '16K5317L',
        'SHEET_AMT' => '11,565',
        'BANK_PAY_AMT' => '11,565',
        'TDS' => '-',
        'PAYABLE' => '11,565',
        'AC_NO' => '115.103.341235',
        'SHEET_TYPE' => 'MBMSP'
    ),
    245 => array(
        'NAME' => 'Ripon Roy',
        'PID' => '08B5319V',
        'SHEET_AMT' => '20,990',
        'BANK_PAY_AMT' => '20,990',
        'TDS' => '-',
        'PAYABLE' => '20,990',
        'AC_NO' => '115.103.341373',
        'SHEET_TYPE' => 'MBMSP'
    ),
    246 => array(
        'NAME' => 'Nazim Uddin',
        'PID' => '96C5320L',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.326438',
        'SHEET_TYPE' => 'MBMSP'
    ),
    247 => array(
        'NAME' => 'Md.Abdul Alim',
        'PID' => '00J5342L',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.326555',
        'SHEET_TYPE' => 'MBMSP'
    ),
    248 => array(
        'NAME' => 'Md. Sojor Uddin',
        'PID' => '05A5346L',
        'SHEET_AMT' => '23,490',
        'BANK_PAY_AMT' => '23,490',
        'TDS' => '-',
        'PAYABLE' => '23,490',
        'AC_NO' => '115.103.357994',
        'SHEET_TYPE' => 'MBMSP'
    ),
    249 => array(
        'NAME' => 'Md. Raihan',
        'PID' => '18C5352V',
        'SHEET_AMT' => '9,165',
        'BANK_PAY_AMT' => '9,165',
        'TDS' => '-',
        'PAYABLE' => '9,165',
        'AC_NO' => '115.103.357989',
        'SHEET_TYPE' => 'MBMSP'
    ),
    250 => array(
        'NAME' => 'Md. Limon Fokir',
        'PID' => '15H5353V',
        'SHEET_AMT' => '9,365',
        'BANK_PAY_AMT' => '9,365',
        'TDS' => '-',
        'PAYABLE' => '9,365',
        'AC_NO' => '115.103.358005',
        'SHEET_TYPE' => 'MBMSP'
    ),
    251 => array(
        'NAME' => 'Md.Shapon Howlader',
        'PID' => '17J5356V',
        'SHEET_AMT' => '15,990',
        'BANK_PAY_AMT' => '15,990',
        'TDS' => '-',
        'PAYABLE' => '15,990',
        'AC_NO' => '115.103.326542',
        'SHEET_TYPE' => 'MBMSP'
    ),
    252 => array(
        'NAME' => 'Ibrahim Sarder',
        'PID' => '17J5358V',
        'SHEET_AMT' => '16,490',
        'BANK_PAY_AMT' => '16,490',
        'TDS' => '-',
        'PAYABLE' => '16,490',
        'AC_NO' => '115.103.358010',
        'SHEET_TYPE' => 'MBMSP'
    ),
    253 => array(
        'NAME' => 'Md. Ziaur Rahman',
        'PID' => '16K5361V',
        'SHEET_AMT' => '15,990',
        'BANK_PAY_AMT' => '15,990',
        'TDS' => '-',
        'PAYABLE' => '15,990',
        'AC_NO' => '115.103.341326',
        'SHEET_TYPE' => 'MBMSP'
    ),
    254 => array(
        'NAME' => 'Mr.Shamim Ahmad',
        'PID' => '18G5362V',
        'SHEET_AMT' => '14,990',
        'BANK_PAY_AMT' => '14,990',
        'TDS' => '-',
        'PAYABLE' => '14,990',
        'AC_NO' => '115.103.363881',
        'SHEET_TYPE' => 'MBMSP'
    ),
    255 => array(
        'NAME' => 'Md. Mamun Shikder',
        'PID' => '19A5364V',
        'SHEET_AMT' => '13,490',
        'BANK_PAY_AMT' => '13,490',
        'TDS' => '-',
        'PAYABLE' => '13,490',
        'AC_NO' => '115.103.390578',
        'SHEET_TYPE' => 'MBMSP'
    ),
    256 => array(
        'NAME' => 'Nurul Afsar',
        'PID' => '18B5368V',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.358052',
        'SHEET_TYPE' => 'MBMSP'
    ),
    257 => array(
        'NAME' => 'JOYDER ALI KHA',
        'PID' => '05E5369V',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.408900',
        'SHEET_TYPE' => 'MBMSP'
    ),
    258 => array(
        'NAME' => 'Md.Robiul Islam',
        'PID' => '14F5371V',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.326478',
        'SHEET_TYPE' => 'MBMSP'
    ),
    259 => array(
        'NAME' => 'Md. Zuael Hossain',
        'PID' => '18B5372V',
        'SHEET_AMT' => '9,665',
        'BANK_PAY_AMT' => '9,665',
        'TDS' => '-',
        'PAYABLE' => '9,665',
        'AC_NO' => '115.103.364296',
        'SHEET_TYPE' => 'MBMSP'
    ),
    260 => array(
        'NAME' => 'Md.  Shohag Talukder',
        'PID' => '18B5375V',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.341401',
        'SHEET_TYPE' => 'MBMSP'
    ),
    261 => array(
        'NAME' => 'Habibur Rahman Haider',
        'PID' => '10L5377V',
        'SHEET_AMT' => '21,990',
        'BANK_PAY_AMT' => '21,990',
        'TDS' => '-',
        'PAYABLE' => '21,990',
        'AC_NO' => '115.103.364478',
        'SHEET_TYPE' => 'MBMSP'
    ),
    262 => array(
        'NAME' => 'Md. Arman Mir',
        'PID' => '19H5379V',
        'SHEET_AMT' => '11,990',
        'BANK_PAY_AMT' => '11,990',
        'TDS' => '-',
        'PAYABLE' => '11,990',
        'AC_NO' => '115.103.429192',
        'SHEET_TYPE' => 'MBMSP'
    ),
    263 => array(
        'NAME' => 'Md. Lutfar Rahman',
        'PID' => '10L5380V',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.364371',
        'SHEET_TYPE' => 'MBMSP'
    ),
    264 => array(
        'NAME' => 'Md. Ayub Sheikh',
        'PID' => '18C5381V',
        'SHEET_AMT' => '15,990',
        'BANK_PAY_AMT' => '15,990',
        'TDS' => '-',
        'PAYABLE' => '15,990',
        'AC_NO' => '115.103.459657',
        'SHEET_TYPE' => 'MBMSP'
    ),
    265 => array(
        'NAME' => 'Md. Asraf Ali',
        'PID' => '14H5383V',
        'SHEET_AMT' => '20,990',
        'BANK_PAY_AMT' => '20,990',
        'TDS' => '-',
        'PAYABLE' => '20,990',
        'AC_NO' => '115.103.326228',
        'SHEET_TYPE' => 'MBMSP'
    ),
    266 => array(
        'NAME' => 'Md. Rajib',
        'PID' => '17M5386V',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.341347',
        'SHEET_TYPE' => 'MBMSP'
    ),
    267 => array(
        'NAME' => 'Md.  Rahmat Molla',
        'PID' => '09G5387V',
        'SHEET_AMT' => '20,490',
        'BANK_PAY_AMT' => '20,490',
        'TDS' => '-',
        'PAYABLE' => '20,490',
        'AC_NO' => '115.103.320983',
        'SHEET_TYPE' => 'MBMSP'
    ),
    268 => array(
        'NAME' => 'Shaikh Kamal Hossain',
        'PID' => '15B5393V',
        'SHEET_AMT' => '22,290',
        'BANK_PAY_AMT' => '22,290',
        'TDS' => '-',
        'PAYABLE' => '22,290',
        'AC_NO' => '115.103.326485',
        'SHEET_TYPE' => 'MBMSP'
    ),
    269 => array(
        'NAME' => 'Yeasin Ali',
        'PID' => '17G5395V',
        'SHEET_AMT' => '9,165',
        'BANK_PAY_AMT' => '9,165',
        'TDS' => '-',
        'PAYABLE' => '9,165',
        'AC_NO' => '115.103.358031',
        'SHEET_TYPE' => 'MBMSP'
    ),
    270 => array(
        'NAME' => 'Md. Ebrahim Faruk',
        'PID' => '18C5396V',
        'SHEET_AMT' => '9,165',
        'BANK_PAY_AMT' => '9,165',
        'TDS' => '-',
        'PAYABLE' => '9,165',
        'AC_NO' => '115.103.341394',
        'SHEET_TYPE' => 'MBMSP'
    ),
    271 => array(
        'NAME' => 'Md. Jalal Miah',
        'PID' => '11L5397V',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.357968',
        'SHEET_TYPE' => 'MBMSP'
    ),
    272 => array(
        'NAME' => 'Md. Babul',
        'PID' => '97E5406S',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.364415',
        'SHEET_TYPE' => 'MBMSP'
    ),
    273 => array(
        'NAME' => 'Md.Sahjahan Mia',
        'PID' => '97K5412S',
        'SHEET_AMT' => '15,490',
        'BANK_PAY_AMT' => '15,490',
        'TDS' => '-',
        'PAYABLE' => '15,490',
        'AC_NO' => '115.103.326737',
        'SHEET_TYPE' => 'MBMSP'
    ),
    274 => array(
        'NAME' => 'Md.  Masud Mia',
        'PID' => '03F5431S',
        'SHEET_AMT' => '14,990',
        'BANK_PAY_AMT' => '14,990',
        'TDS' => '-',
        'PAYABLE' => '14,990',
        'AC_NO' => '115.103.321975',
        'SHEET_TYPE' => 'MBMSP'
    ),
    275 => array(
        'NAME' => 'Md. Babul Mia',
        'PID' => '92E5451Q',
        'SHEET_AMT' => '50,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.326006',
        'SHEET_TYPE' => 'MBMSP'
    ),
    276 => array(
        'NAME' => 'Md.  Zillur Rahman',
        'PID' => '96E5455Q',
        'SHEET_AMT' => '26,990',
        'BANK_PAY_AMT' => '26,990',
        'TDS' => '-',
        'PAYABLE' => '26,990',
        'AC_NO' => '115.103.321218',
        'SHEET_TYPE' => 'MBMSP'
    ),
    277 => array(
        'NAME' => 'Md. Faruk Hossain',
        'PID' => '98B5462Q',
        'SHEET_AMT' => '48,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.325988',
        'SHEET_TYPE' => 'MBMSP'
    ),
    278 => array(
        'NAME' => 'Md.  Delwar Hossain',
        'PID' => '00B5471Q',
        'SHEET_AMT' => '35,890',
        'BANK_PAY_AMT' => '25,000',
        'TDS' => '-',
        'PAYABLE' => '25,000',
        'AC_NO' => '115.103.321015',
        'SHEET_TYPE' => 'MBMSP'
    ),
    279 => array(
        'NAME' => 'Md. Ataur Rahman',
        'PID' => '17K5503Q',
        'SHEET_AMT' => '11,990',
        'BANK_PAY_AMT' => '11,990',
        'TDS' => '-',
        'PAYABLE' => '11,990',
        'AC_NO' => '115.103.339763',
        'SHEET_TYPE' => 'MBMSP'
    ),
    280 => array(
        'NAME' => 'Kasem',
        'PID' => '07H5725K',
        'SHEET_AMT' => '15,790',
        'BANK_PAY_AMT' => '15,790',
        'TDS' => '-',
        'PAYABLE' => '15,790',
        'AC_NO' => '115.103.326277',
        'SHEET_TYPE' => 'MBMSP'
    ),
    281 => array(
        'NAME' => 'Sunil Kanii Chakma',
        'PID' => '01H5827U',
        'SHEET_AMT' => '21,890',
        'BANK_PAY_AMT' => '21,890',
        'TDS' => '-',
        'PAYABLE' => '21,890',
        'AC_NO' => '115.103.326672',
        'SHEET_TYPE' => 'MBMSP'
    ),
    282 => array(
        'NAME' => 'Golam Mostofa',
        'PID' => '05E5834U',
        'SHEET_AMT' => '30,990',
        'BANK_PAY_AMT' => '30,990',
        'TDS' => '-',
        'PAYABLE' => '30,990',
        'AC_NO' => '115.103.325558',
        'SHEET_TYPE' => 'MBMSP'
    ),
    283 => array(
        'NAME' => 'Mst Anwar Hossain',
        'PID' => '00B5840U',
        'SHEET_AMT' => '34,790',
        'BANK_PAY_AMT' => '34,790',
        'TDS' => '-',
        'PAYABLE' => '34,790',
        'AC_NO' => '115.103.325969',
        'SHEET_TYPE' => 'MBMSP'
    ),
    284 => array(
        'NAME' => 'Proshanta Kumar Biswas',
        'PID' => '00C5845U',
        'SHEET_AMT' => '28,390',
        'BANK_PAY_AMT' => '28,390',
        'TDS' => '-',
        'PAYABLE' => '28,390',
        'AC_NO' => '115.103.325472',
        'SHEET_TYPE' => 'MBMSP'
    ),
    285 => array(
        'NAME' => 'A.M. Masud Rana',
        'PID' => '09M5859U',
        'SHEET_AMT' => '23,990',
        'BANK_PAY_AMT' => '23,990',
        'TDS' => '-',
        'PAYABLE' => '23,990',
        'AC_NO' => '115.103.334821',
        'SHEET_TYPE' => 'MBMSP'
    ),
    286 => array(
        'NAME' => 'Md.  Harun Ar Rashid',
        'PID' => '98J6121G',
        'SHEET_AMT' => '34,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.321253',
        'SHEET_TYPE' => 'MBMSP'
    ),
    287 => array(
        'NAME' => 'Md.Shamim',
        'PID' => '13C6161G',
        'SHEET_AMT' => '17,490',
        'BANK_PAY_AMT' => '17,490',
        'TDS' => '-',
        'PAYABLE' => '17,490',
        'AC_NO' => '115.103.460798',
        'SHEET_TYPE' => 'MBMSP'
    ),
    288 => array(
        'NAME' => 'Jewel Chakma',
        'PID' => '99D6297G',
        'SHEET_AMT' => '18,990',
        'BANK_PAY_AMT' => '18,990',
        'TDS' => '-',
        'PAYABLE' => '18,990',
        'AC_NO' => '115.103.325516',
        'SHEET_TYPE' => 'MBMSP'
    ),
    289 => array(
        'NAME' => 'Md.Fazlu',
        'PID' => '91G6320G',
        'SHEET_AMT' => '23,290',
        'BANK_PAY_AMT' => '23,290',
        'TDS' => '-',
        'PAYABLE' => '23,290',
        'AC_NO' => '115.103.326326',
        'SHEET_TYPE' => 'MBMSP'
    ),
    290 => array(
        'NAME' => 'Md. Shoket Hossen',
        'PID' => '94H6342G',
        'SHEET_AMT' => '30,990',
        'BANK_PAY_AMT' => '30,990',
        'TDS' => '-',
        'PAYABLE' => '30,990',
        'AC_NO' => '115.103.320616',
        'SHEET_TYPE' => 'MBMSP'
    ),
    291 => array(
        'NAME' => 'Md.  Meher Ali',
        'PID' => '92G6429G',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.320728',
        'SHEET_TYPE' => 'MBMSP'
    ),
    292 => array(
        'NAME' => 'Md.  Golam Mostofa  Hridoy',
        'PID' => '02G6477G',
        'SHEET_AMT' => '25,990',
        'BANK_PAY_AMT' => '25,990',
        'TDS' => '-',
        'PAYABLE' => '25,990',
        'AC_NO' => '115.103.320707',
        'SHEET_TYPE' => 'MBMSP'
    ),
    293 => array(
        'NAME' => 'Md.Rafique',
        'PID' => '03M6556G',
        'SHEET_AMT' => '22,232',
        'BANK_PAY_AMT' => '22,232',
        'TDS' => '-',
        'PAYABLE' => '22,232',
        'AC_NO' => '115.103.326688',
        'SHEET_TYPE' => 'MBMSP'
    ),
    294 => array(
        'NAME' => 'Md.Shimul Howladar',
        'PID' => '13A6567G',
        'SHEET_AMT' => '19,190',
        'BANK_PAY_AMT' => '19,190',
        'TDS' => '-',
        'PAYABLE' => '19,190',
        'AC_NO' => '115.103.459592',
        'SHEET_TYPE' => 'MBMSP'
    ),
    295 => array(
        'NAME' => 'Md.Alamgir Hosssain Milon',
        'PID' => '01A6693G',
        'SHEET_AMT' => '24,890',
        'BANK_PAY_AMT' => '24,890',
        'TDS' => '-',
        'PAYABLE' => '24,890',
        'AC_NO' => '115.103.325724',
        'SHEET_TYPE' => 'MBMSP'
    ),
    296 => array(
        'NAME' => 'Md.  Babul',
        'PID' => '98B6815G',
        'SHEET_AMT' => '35,990',
        'BANK_PAY_AMT' => '20,000',
        'TDS' => '-',
        'PAYABLE' => '20,000',
        'AC_NO' => '115.103.321244',
        'SHEET_TYPE' => 'MBMSP'
    ),
    297 => array(
        'NAME' => 'Md. Nadim Parvez',
        'PID' => '15L7208Y',
        'SHEET_AMT' => '19,990',
        'BANK_PAY_AMT' => '19,990',
        'TDS' => '-',
        'PAYABLE' => '19,990',
        'AC_NO' => '115.103.335790',
        'SHEET_TYPE' => 'MBMSP'
    ),
    298 => array(
        'NAME' => 'Mr. Md. Lieakot Ali',
        'PID' => '89L0232C',
        'SHEET_AMT' => '51,490',
        'BANK_PAY_AMT' => '35,000',
        'TDS' => '-',
        'PAYABLE' => '35,000',
        'AC_NO' => '115.103.263611',
        'SHEET_TYPE' => 'MBMWP'
    ),
    299 => array(
        'NAME' => 'Md. Shahidullah',
        'PID' => '13J0359C',
        'SHEET_AMT' => '44,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.245807',
        'SHEET_TYPE' => 'MBMWP'
    ),
    300 => array(
        'NAME' => 'Mr. Abdur Rakib',
        'PID' => '16K0745D',
        'SHEET_AMT' => '28,490',
        'BANK_PAY_AMT' => '28,490',
        'TDS' => '-',
        'PAYABLE' => '28,490',
        'AC_NO' => '115.103.313352',
        'SHEET_TYPE' => 'MBMWP'
    ),
    301 => array(
        'NAME' => 'Md. Jahurul Islam',
        'PID' => '96A3012R',
        'SHEET_AMT' => '52,490',
        'BANK_PAY_AMT' => '40,000',
        'TDS' => '417',
        'PAYABLE' => '39,583',
        'AC_NO' => '115.103.178547',
        'SHEET_TYPE' => 'MBMWP'
    ),
    302 => array(
        'NAME' => 'Md.  Mozammel Hoque',
        'PID' => '98H3089R',
        'SHEET_AMT' => '32,190',
        'BANK_PAY_AMT' => '32,190',
        'TDS' => '-',
        'PAYABLE' => '32,190',
        'AC_NO' => '115.103.322080',
        'SHEET_TYPE' => 'MBMWP'
    ),
    303 => array(
        'NAME' => 'Shafiul Alam',
        'PID' => '05A4136H',
        'SHEET_AMT' => '18,790',
        'BANK_PAY_AMT' => '18,790',
        'TDS' => '-',
        'PAYABLE' => '18,790',
        'AC_NO' => '115.103.321377',
        'SHEET_TYPE' => 'MBMWP'
    ),
    304 => array(
        'NAME' => 'Md.Ab Mannan Sharkar',
        'PID' => '96E4154H',
        'SHEET_AMT' => '21,790',
        'BANK_PAY_AMT' => '21,790',
        'TDS' => '-',
        'PAYABLE' => '21,790',
        'AC_NO' => '115.103.464760',
        'SHEET_TYPE' => 'MBMWP'
    ),
    305 => array(
        'NAME' => 'Mr. Md. Mamun',
        'PID' => '99E4610J',
        'SHEET_AMT' => '46,490',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.263606',
        'SHEET_TYPE' => 'MBMWP'
    ),
    306 => array(
        'NAME' => 'Miraz Miah',
        'PID' => '96G4675J',
        'SHEET_AMT' => '48,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '115.103.326209',
        'SHEET_TYPE' => 'MBMWP'
    ),
    307 => array(
        'NAME' => 'Montu',
        'PID' => '06H5007P',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.326604',
        'SHEET_TYPE' => 'MBMWP'
    ),
    308 => array(
        'NAME' => 'Md.Rafiqul Islam',
        'PID' => '08F5014P',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.326721',
        'SHEET_TYPE' => 'MBMWP'
    ),
    309 => array(
        'NAME' => 'Md.  Tazul Islam',
        'PID' => '13J5018P',
        'SHEET_AMT' => '24,990',
        'BANK_PAY_AMT' => '24,990',
        'TDS' => '-',
        'PAYABLE' => '24,990',
        'AC_NO' => '115.103.322007',
        'SHEET_TYPE' => 'MBMWP'
    ),
    310 => array(
        'NAME' => 'Md. Mojibur Rahman',
        'PID' => '89L5023P',
        'SHEET_AMT' => '28,108',
        'BANK_PAY_AMT' => '28,108',
        'TDS' => '-',
        'PAYABLE' => '28,108',
        'AC_NO' => '115.103.320588',
        'SHEET_TYPE' => 'MBMWP'
    ),
    311 => array(
        'NAME' => 'Md Jahidul Islam',
        'PID' => '13L5041P',
        'SHEET_AMT' => '18,490',
        'BANK_PAY_AMT' => '18,490',
        'TDS' => '-',
        'PAYABLE' => '18,490',
        'AC_NO' => '115.103.326240',
        'SHEET_TYPE' => 'MBMWP'
    ),
    312 => array(
        'NAME' => 'Md.  Abu Taher',
        'PID' => '14E5050P',
        'SHEET_AMT' => '19,490',
        'BANK_PAY_AMT' => '19,490',
        'TDS' => '-',
        'PAYABLE' => '19,490',
        'AC_NO' => '115.103.322033',
        'SHEET_TYPE' => 'MBMWP'
    ),
    313 => array(
        'NAME' => 'Md. Mostofa Kamal',
        'PID' => '02M5058P',
        'SHEET_AMT' => '20,990',
        'BANK_PAY_AMT' => '20,990',
        'TDS' => '-',
        'PAYABLE' => '20,990',
        'AC_NO' => '115.103.320621',
        'SHEET_TYPE' => 'MBMWP'
    ),
    314 => array(
        'NAME' => 'Sree Mukul Chandra Das',
        'PID' => '13G5147P',
        'SHEET_AMT' => '14,490',
        'BANK_PAY_AMT' => '14,490',
        'TDS' => '-',
        'PAYABLE' => '14,490',
        'AC_NO' => '115.103.336798',
        'SHEET_TYPE' => 'MBMWP'
    ),
    315 => array(
        'NAME' => 'Md.  Jahangir Alam',
        'PID' => '05G5235M',
        'SHEET_AMT' => '29,990',
        'BANK_PAY_AMT' => '29,990',
        'TDS' => '-',
        'PAYABLE' => '29,990',
        'AC_NO' => '115.103.321185',
        'SHEET_TYPE' => 'MBMWP'
    ),
    316 => array(
        'NAME' => 'Md.Zinnat Ali Sikder',
        'PID' => '12L5243M',
        'SHEET_AMT' => '28,090',
        'BANK_PAY_AMT' => '28,090',
        'TDS' => '-',
        'PAYABLE' => '28,090',
        'AC_NO' => '115.103.326214',
        'SHEET_TYPE' => 'MBMWP'
    ),
    317 => array(
        'NAME' => 'Md. Torikul Islam',
        'PID' => '13G5805U',
        'SHEET_AMT' => '16,690',
        'BANK_PAY_AMT' => '16,690',
        'TDS' => '-',
        'PAYABLE' => '16,690',
        'AC_NO' => '115.103.320504',
        'SHEET_TYPE' => 'MBMWP'
    ),
    318 => array(
        'NAME' => 'Jweel Islam',
        'PID' => '13B5904U',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.320941',
        'SHEET_TYPE' => 'MBMWP'
    ),
    319 => array(
        'NAME' => 'Md. Mustafizur Rahman',
        'PID' => '15K7207Y',
        'SHEET_AMT' => '164,990',
        'BANK_PAY_AMT' => '60,000',
        'TDS' => '500',
        'PAYABLE' => '59,500',
        'AC_NO' => '115.103.249952',
        'SHEET_TYPE' => 'MBMWP'
    ),
    320 => array(
        'NAME' => 'Md.Nazim Uddin',
        'PID' => '17H7219Y',
        'SHEET_AMT' => '21,490',
        'BANK_PAY_AMT' => '21,490',
        'TDS' => '-',
        'PAYABLE' => '21,490',
        'AC_NO' => '115.103.326170',
        'SHEET_TYPE' => 'MBMWP'
    ),
    321 => array(
        'NAME' => 'Md. Salim Reza',
        'PID' => '18B7222Y',
        'SHEET_AMT' => '18,490',
        'BANK_PAY_AMT' => '18,490',
        'TDS' => '-',
        'PAYABLE' => '18,490',
        'AC_NO' => '115.103.336852',
        'SHEET_TYPE' => 'MBMWP'
    ),
    322 => array(
        'NAME' => 'Amin Muhtasim',
        'PID' => '18C7224Y',
        'SHEET_AMT' => '13,990',
        'BANK_PAY_AMT' => '13,990',
        'TDS' => '-',
        'PAYABLE' => '13,990',
        'AC_NO' => '115.103.336686',
        'SHEET_TYPE' => 'MBMWP'
    ),
    323 => array(
        'NAME' => 'Mr.Razaul',
        'PID' => '16M8508P',
        'SHEET_AMT' => '25,990',
        'BANK_PAY_AMT' => '25,990',
        'TDS' => '-',
        'PAYABLE' => '25,990',
        'AC_NO' => '115.103.315357',
        'SHEET_TYPE' => 'MBMWP'
    ),
    324 => array(
        'NAME' => 'Md.Mehedi Hasan Shekh',
        'PID' => '17M4003H',
        'SHEET_AMT' => '16,196',
        'BANK_PAY_AMT' => '16,196',
        'TDS' => '-',
        'PAYABLE' => '16,196',
        'AC_NO' => '115.103.464713',
        'SHEET_TYPE' => 'SRT'
    ),
    325 => array(
        'NAME' => 'Md.Mizanur Rahman Mizan',
        'PID' => '98G4062H',
        'SHEET_AMT' => '24,790',
        'BANK_PAY_AMT' => '24,790',
        'TDS' => '-',
        'PAYABLE' => '24,790',
        'AC_NO' => '115.103.326417',
        'SHEET_TYPE' => 'SRT'
    ),
    326 => array(
        'NAME' => 'Md. Rezaul Karim',
        'PID' => '05J4066H',
        'SHEET_AMT' => '18,490',
        'BANK_PAY_AMT' => '18,490',
        'TDS' => '-',
        'PAYABLE' => '18,490',
        'AC_NO' => '115.103.370851',
        'SHEET_TYPE' => 'SRT'
    ),
    327 => array(
        'NAME' => 'Mr.Shariful Islam',
        'PID' => '18A4201H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.464776',
        'SHEET_TYPE' => 'SRT'
    ),
    328 => array(
        'NAME' => 'Md.Firoz Reza',
        'PID' => '10G6242G',
        'SHEET_AMT' => '16,990',
        'BANK_PAY_AMT' => '16,990',
        'TDS' => '-',
        'PAYABLE' => '16,990',
        'AC_NO' => '115.103.464498',
        'SHEET_TYPE' => 'SRT'
    ),
    329 => array(
        'NAME' => 'Md.  Masud Rana',
        'PID' => '03M6469G',
        'SHEET_AMT' => '29,990',
        'BANK_PAY_AMT' => '29,990',
        'TDS' => '-',
        'PAYABLE' => '29,990',
        'AC_NO' => '115.103.320712',
        'SHEET_TYPE' => 'SRT'
    ),
    330 => array(
        'NAME' => 'Amina Khatun Joly',
        'PID' => '17M7312B',
        'SHEET_AMT' => '22,990',
        'BANK_PAY_AMT' => '22,990',
        'TDS' => '-',
        'PAYABLE' => '22,990',
        'AC_NO' => '115.103.329653',
        'SHEET_TYPE' => 'SRT'
    ),
    331 => array(
        'NAME' => 'Mohammad Abul Hossain',
        'PID' => '18C7327C',
        'SHEET_AMT' => '23,290',
        'BANK_PAY_AMT' => '23,290',
        'TDS' => '-',
        'PAYABLE' => '23,290',
        'AC_NO' => '115.103.339742',
        'SHEET_TYPE' => 'SRT'
    ),
    332 => array(
        'NAME' => 'Sujan Sarker',
        'PID' => '18D7328C',
        'SHEET_AMT' => '15,490',
        'BANK_PAY_AMT' => '15,490',
        'TDS' => '-',
        'PAYABLE' => '15,490',
        'AC_NO' => '115.103.340040',
        'SHEET_TYPE' => 'SRT'
    ),
    333 => array(
        'NAME' => 'Md. Saidul Islam',
        'PID' => '18G7332C',
        'SHEET_AMT' => '55,990',
        'BANK_PAY_AMT' => '55,990',
        'TDS' => '500',
        'PAYABLE' => '55,490',
        'AC_NO' => '115.103.352977',
        'SHEET_TYPE' => 'SRT'
    ),
    334 => array(
        'NAME' => 'Md. Nazmuz Shakib',
        'PID' => '19K7340C',
        'SHEET_AMT' => '54,990',
        'BANK_PAY_AMT' => '41,000',
        'TDS' => '-',
        'PAYABLE' => '41,000',
        'AC_NO' => '179.151.168906',
        'SHEET_TYPE' => 'SRT'
    ),
    335 => array(
        'NAME' => 'Md.Mostofa',
        'PID' => '19K7341C',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.464742',
        'SHEET_TYPE' => 'SRT'
    ),
    336 => array(
        'NAME' => 'Abdulla Fakir Rana',
        'PID' => '18B8807H',
        'SHEET_AMT' => '17,990',
        'BANK_PAY_AMT' => '17,990',
        'TDS' => '-',
        'PAYABLE' => '17,990',
        'AC_NO' => '115.103.464729',
        'SHEET_TYPE' => 'SRT'
    ),
    337 => array(
        'NAME' => 'Mr. Bakul Chakma',
        'PID' => '18G9237M',
        'SHEET_AMT' => '42,790',
        'BANK_PAY_AMT' => '42,790',
        'TDS' => '-',
        'PAYABLE' => '42,790',
        'AC_NO' => '227.103.0017443',
        'SHEET_TYPE' => 'SRT'
    ),
    338 => array(
        'NAME' => 'Md. Ibrahim',
        'PID' => '18J9239M',
        'SHEET_AMT' => '32,590',
        'BANK_PAY_AMT' => '32,590',
        'TDS' => '-',
        'PAYABLE' => '32,590',
        'AC_NO' => '115.151.0062483',
        'SHEET_TYPE' => 'SRT'
    ),
    339 => array(
        'NAME' => 'Md. Monirul Islam',
        'PID' => '19G9241M',
        'SHEET_AMT' => '39,990',
        'BANK_PAY_AMT' => '30,000',
        'TDS' => '-',
        'PAYABLE' => '30,000',
        'AC_NO' => '151.151.0215286',
        'SHEET_TYPE' => 'SRT'
    ),
);

        $getEmployee = DB::table('hr_as_basic_info')
        ->leftJoin('hr_benefits', 'hr_as_basic_info.associate_id', 'hr_benefits.ben_as_id')
        ->select('hr_as_basic_info.associate_id', 'hr_as_basic_info.as_oracle_code', 'hr_benefits.*')
        ->get();
        // dd($getEmployee);
        $count = 0;
        $macth = [];
        $noMacth = [];
        foreach ($getEmployee as $emp) {
            //$amount = $emp->ben_cash_amount + $emp->ben_bank_amount;
            foreach ($getData as $key => $value) {
                if($value['PID'] == $emp->as_oracle_code){
                    ++$count;
                    $macth[$emp->as_oracle_code] = $emp->ben_id;
                }elseif($value['PID'] != $emp->as_oracle_code){
                    $noMacth[] = $emp->as_oracle_code;
                }
            }
        }
        return ($macth);

    	$trainingNames = TrainingNames::where('hr_tr_status', '1')
    					->pluck('hr_tr_name', 'hr_tr_name_id');

    	return view('hr/training/add_training', compact('trainingNames'));
    }

    # Store Training
    public function saveTraining(Request $request)
    {
        //ACL::check(["permission" => "hr_training_add"]);
        #-----------------------------------------------------------#

    	$validator = Validator::make($request->all(), [
            'tr_as_tr_id'     => 'required|max:11',
            'tr_trainer_name' => 'required|max:128',
            'tr_description'  => 'required|max:1024',
            'tr_start_date'   => 'required|date',
            'tr_end_date'     => 'date|nullable',
            'tr_start_time'   => 'required|max:5',
            'tr_end_time'     => 'required|max:5'
        ]);


        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------Store Data---------------------
        	$store = new Training;
			$store->tr_as_tr_id  = $request->tr_as_tr_id;
			$store->tr_trainer_name = $request->tr_trainer_name;
			$store->tr_description = $request->tr_description;
			$store->tr_start_date = (!empty($request->tr_start_date)?date('Y-m-d',strtotime($request->tr_start_date)):null);
			$store->tr_end_date = (!empty($request->tr_end_date)?date('Y-m-d',strtotime($request->tr_end_date)):null);
			$store->tr_start_time = (!empty($request->tr_start_time)?date('H:i',strtotime($request->tr_start_time)):null);
			$store->tr_end_time = (!empty($request->tr_end_time)?date('H:i',strtotime($request->tr_end_time)):null);

			if ($store->save())
			{
                $this->logFileWrite("Training Entry Saved", $store->tr_id);
        		return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
			}
			else
			{
        		return back()
        			->withInput()->with('error', 'Please try again.');
			}
        }
    }


    # training list
    public function trainingList()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        return view('hr/training/training_list');
    }

    # training data
    public function getData()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_training AS tr')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'tr.*',
                'tn.hr_tr_name AS training_name'
            )
            ->leftJoin('hr_training_names AS tn','tn.hr_tr_name_id', '=', 'tr.tr_as_tr_id')
            ->orderBy('tr.tr_start_date','desc')
            ->orderBy('tr.tr_id','desc')
            ->get();

        return DataTables::of($data)
            ->addColumn('schedule_date', function ($data) {

                if($data->tr_start_date != null)
                {
                    $start_date=date('d-M-Y',strtotime($data->tr_start_date));

                    if (!empty($data->tr_end_date))
                    {
                        $end_date=date('d-M-Y',strtotime($data->tr_end_date));
                    }
                    else
                    {
                        $end_date = "Continue";
                    }

                    return "<strong>Start : </strong><span>$start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$end_date</span>";
                }
                else
                {
                    return "<strong>Start : </strong><span>$data->tr_start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_date</span>";
                }
            })
            ->addColumn('schedule_time', function ($data) {
                return "<strong>Start : </strong><span>$data->tr_start_time</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_time</span>";
            })
            ->addColumn('action', function ($data) {
                if ($data->tr_status == 1)
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-success' style='width:55px;'>Active</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/inactive")." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Inactive Now!\" style='width:29px;'>
                            <i class=\"ace-icon fa fa-times bigger-120\"></i>
                        </div>";
                else
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-danger'>Inactive</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/active")." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Active Now!\">
                            <i class=\"ace-icon fa fa-check bigger-120\"></i>
                        </div>";
            })
            ->rawColumns(['serial_no', 'schedule_date', 'schedule_time', 'action'])
            ->toJson();
    }


    # training Status
    public function trainingStatus(Request $request)
    {

        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        if ($request->status == 'active')
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'1']);

            $this->logFileWrite("Training Activated", $request->id);
            return back()->with('success', 'Training is Activated!');
        }
        else
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'0']);

            $this->logFileWrite("Training Inactivated", $request->id);
            return back()->with('success', 'Training is Inactivated!');

        }

    }
}
