<?php
$employee = DB::table('hr_as_basic_info')
                    ->where('as_doj', '>','2020-08-04')
                    ->where('as_ot', 1)
                    ->whereNotIn('as_location', [12,13])
                    ->whereIn('as_unit_id', [1,4,5])
                    ->pluck('associate_id');
        
        foreach ($employee as $key => $emp) {
            HolidayRoaster::firstOrCreate(
                ['as_id' => $emp, 'date' => '2020-09-18'],
                [
                    'year' => '2020',
                    'month' => '09',
                    'as_id' => $emp,
                    'date' => '2020-09-18',
                    'remarks' => 'OT',
                    'comment' => 'Instead of 2020-08-04'
                ]
            );
        }
        
        dd($employee);
        $dates = array(
            '2020-09-01',
            '2020-09-02',
            '2020-09-03',
            '2020-09-04',
            '2020-09-05',
            '2020-09-06',
            '2020-09-07', 
            '2020-09-08',
            '2020-09-09',
            '2020-09-10',
            '2020-09-11',
            '2020-09-12',
            '2020-09-13',
            '2020-09-14',
            '2020-09-15'

        );
        $test = [];
        foreach ($dates as $key => $date) {
            $att = DB::table('hr_attendance_mbm AS m')
                ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'm.as_id')
                ->where('m.in_date', $date)
                ->pluck('b.associate_id');


            $test[$date] = DB::table('hr_absent')
                ->where('date',$date)
                ->whereIn('associate_id', $att)
                ->delete();

        }