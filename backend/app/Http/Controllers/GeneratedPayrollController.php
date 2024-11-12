<?php

namespace App\Http\Controllers;

use App\Models\GeneratedPayroll;
use App\Models\AdjustmentPayroll;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneratedPayrollController extends Controller
{ 

    public function saveGeneratedFullTimePayroll(Request $request)
    {
        $payrollData = $request->input('payroll_data');

        if (is_null($payrollData) || !is_array($payrollData)) {
            return response()->json(['error' => 'Invalid payroll data provided.'], 400);
        }

        $currentTimestamp = Carbon::now();
        foreach ($payrollData as &$data) {
            $data['created_at'] = $currentTimestamp;
            $data['updated_at'] = $currentTimestamp;
        }

        try {
            DB::table('generated_payroll')->insert($payrollData);
            return response()->json(['message' => 'Payroll records saved successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkExistingPayroll(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $payrollType = $request->input('payroll_type');

        $existingPayrolls = DB::table('generated_payroll')
            ->where('date_from', $dateFrom)
            ->where('date_to', $dateTo)
            ->where('payroll_type', $payrollType)
            ->get(['faculty_id']);

        return response()->json($existingPayrolls);
    }

 
    public function getPayrollFullTimeHistory(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $data = DB::table('faculty AS f')
            ->join('department AS d', 'f.department_id', '=', 'd.id')
            ->leftJoin('faculty_rates AS fr', 'f.id', '=', 'fr.faculty_id')
            ->leftJoin('generated_payroll AS gp', 'f.id', '=', 'gp.faculty_id')
            ->leftJoin('adjustment_payroll AS ap', 'gp.id', '=', 'ap.generated_payroll_id')
            ->where('gp.payroll_type', 'ft_payroll')
            ->where('gp.date_from', '>=', $dateFrom)
            ->where('gp.date_to', '<=', $dateTo)
            ->select(
                'gp.id',
                'f.id AS faculty_id',
                DB::raw('CONCAT(f.last_name, " ", f.first_name) AS full_name'),
                'fr.rate_type',
                'fr.rate_value',
                'gp.date_from',
                'gp.date_to',
                'gp.hours_or_days',
                'gp.gross_amount',
                DB::raw('DATE_FORMAT(gp.late, "%H:%i") AS late'),
                'gp.tax',
                'gp.netpay',
                'gp.payroll_type',
                'ap.adjustment',
                'ap.adjusted_netpay',
                DB::raw('CASE 
                WHEN fr.rate_value = 150 THEN 15000 
                WHEN fr.rate_value = 170 THEN 17000 
                WHEN fr.rate_value = 250 THEN 20000 
                WHEN fr.rate_value = 350 THEN 25000 
                ELSE 0 
            END AS monthly_rate'),
                DB::raw('
                CASE 
                    WHEN gp.late IS NULL OR gp.late = "" THEN 0
                    ELSE (TIME_TO_SEC(gp.late) / 60) * fr.rate_value / 60 
                END AS late_amount
            ')
            )
            ->orderBy('full_name', 'ASC')
            ->get();

        return response()->json($data);
    }


    public function getPayrollPartTimeHistory(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $data = DB::table('faculty AS f')
            ->join('department AS d', 'f.department_id', '=', 'd.id')
            ->leftJoin('faculty_rates AS fr', 'f.id', '=', 'fr.faculty_id')
            ->leftJoin('generated_payroll AS gp', 'f.id', '=', 'gp.faculty_id')
            ->leftJoin('adjustment_payroll AS ap', 'gp.id', '=', 'ap.generated_payroll_id')
            ->where('gp.payroll_type', 'pt_payroll')
            ->where('gp.date_from', '>=', $dateFrom)
            ->where('gp.date_to', '<=', $dateTo)
            ->select(
                'gp.id',
                'f.id AS faculty_id',
                DB::raw('CONCAT(f.last_name, " ", f.first_name) AS full_name'),
                'fr.rate_type',
                'fr.rate_value',
                'gp.date_from',
                'gp.date_to',
                'gp.hours_or_days',
                'gp.gross_amount',
                DB::raw('DATE_FORMAT(gp.late, "%H:%i") AS late'),
                'gp.tax',
                'gp.netpay',
                'gp.payroll_type',
                'ap.adjustment',
                'ap.adjusted_netpay',
                DB::raw('CASE 
                WHEN fr.rate_value = 150 THEN 15000 
                WHEN fr.rate_value = 170 THEN 17000 
                WHEN fr.rate_value = 250 THEN 20000 
                WHEN fr.rate_value = 350 THEN 25000 
                ELSE 0 
            END AS monthly_rate'),
                DB::raw('
                CASE 
                    WHEN gp.late IS NULL OR gp.late = "" THEN 0
                    ELSE (TIME_TO_SEC(gp.late) / 60) * fr.rate_value / 60 
                END AS late_amount
            ')
            )
            ->orderBy('full_name', 'ASC')
            ->get();

        return response()->json($data);
    }

    public function getPayrollPartTimeRegularHistory(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $data = DB::table('faculty AS f')
            ->join('department AS d', 'f.department_id', '=', 'd.id')
            ->leftJoin('faculty_rates AS fr', 'f.id', '=', 'fr.faculty_id')
            ->leftJoin('generated_payroll AS gp', 'f.id', '=', 'gp.faculty_id')
            ->leftJoin('adjustment_payroll AS ap', 'gp.id', '=', 'ap.generated_payroll_id')
            ->where('gp.payroll_type', 'ptr_payroll')
            ->where('gp.date_from', '>=', $dateFrom)
            ->where('gp.date_to', '<=', $dateTo)
            ->select(
                'gp.id',
                'f.id AS faculty_id',
                DB::raw('CONCAT(f.last_name, " ", f.first_name) AS full_name'),
                'fr.rate_type',
                'fr.rate_value',
                'gp.date_from',
                'gp.date_to',
                'gp.hours_or_days',
                'gp.gross_amount',
                DB::raw('DATE_FORMAT(gp.late, "%H:%i") AS late'),
                'gp.tax',
                'gp.netpay',
                'gp.payroll_type',
                'ap.adjustment',
                'ap.adjusted_netpay',
                DB::raw('CASE 
                WHEN fr.rate_value = 150 THEN 15000 
                WHEN fr.rate_value = 170 THEN 17000 
                WHEN fr.rate_value = 250 THEN 20000 
                WHEN fr.rate_value = 350 THEN 25000 
                ELSE 0 
            END AS monthly_rate'),
                DB::raw('
                CASE 
                    WHEN gp.late IS NULL OR gp.late = "" THEN 0
                    ELSE (TIME_TO_SEC(gp.late) / 60) * fr.rate_value / 60 
                END AS late_amount
            ')
            )
            ->orderBy('full_name', 'ASC')
            ->get();

        return response()->json($data);
    }

    public function getPayrollProgramHeadsHistory(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $data = DB::table('faculty AS f')
            ->join('department AS d', 'f.department_id', '=', 'd.id')
            ->leftJoin('faculty_rates AS fr', 'f.id', '=', 'fr.faculty_id')
            ->leftJoin('generated_payroll AS gp', 'f.id', '=', 'gp.faculty_id')
            ->leftJoin('adjustment_payroll AS ap', 'gp.id', '=', 'ap.generated_payroll_id')
            ->where('gp.payroll_type', 'ph_payroll')
            ->where('gp.date_from', '>=', $dateFrom)
            ->where('gp.date_to', '<=', $dateTo)
            ->select(
                'gp.id',
                'f.id AS faculty_id', 
                DB::raw('CONCAT(f.last_name, " ", f.first_name) AS full_name'),
                'fr.rate_type',
                'fr.rate_value',
                'gp.date_from',
                'gp.date_to',
                'gp.hours_or_days',
                'gp.gross_amount',
                 DB::raw('DATE_FORMAT(gp.late, "%H:%i") AS late'),
                'gp.tax',
                'gp.netpay',
                'gp.payroll_type',
                'ap.adjustment',
                'ap.adjusted_netpay',
                DB::raw('CASE 
                WHEN f.faculty_type = "department_head" AND fr.rate_type = "doctor" THEN 30000
                WHEN f.faculty_type = "department_head" AND fr.rate_value = 275 THEN 30000
                WHEN fr.rate_value = 150 THEN 15000 
                WHEN fr.rate_value = 170 THEN 17000 
                WHEN fr.rate_value = 250 THEN 20000 
                WHEN fr.rate_value = 350 THEN 25000 
                ELSE 0 
            END AS monthly_rate'),
                DB::raw('
                CASE 
                    WHEN gp.late IS NULL OR gp.late = "" THEN 0
                    ELSE (TIME_TO_SEC(gp.late) / 60) * fr.rate_value / 60 
                END AS late_amount
            ')
            )
            ->orderBy('full_name', 'ASC')
            ->get();

        return response()->json($data);
    }

 
    public function updatePayrollAdjustment(Request $request)
    {
        $request->validate([
            'payroll_id' => 'required|exists:generated_payroll,id',
            'adjustment' => 'required|numeric',
        ]);

        $payroll = GeneratedPayroll::find($request->input('payroll_id'));

        $adjustment = $request->input('adjustment');
        $adjustedNetpay = $payroll->netpay + $adjustment;

        $existingAdjustment = AdjustmentPayroll::where('generated_payroll_id', $payroll->id)->first();

        if ($existingAdjustment) {
            $existingAdjustment->adjustment = $adjustment;
            $existingAdjustment->adjusted_netpay = $adjustedNetpay;
            $existingAdjustment->save();
        } else {
            $adjustmentRecord = new AdjustmentPayroll();
            $adjustmentRecord->generated_payroll_id = $payroll->id;
            $adjustmentRecord->adjustment = $adjustment;
            $adjustmentRecord->adjusted_netpay = $adjustedNetpay;
            $adjustmentRecord->save();
        }
 

        return response()->json(['message' => 'Adjustment updated successfully.'], 200);
    }






}
