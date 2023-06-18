<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use DateTime;


class QuoteController extends Controller
{
    /**
     * Create a new QuoteController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Create a new QuoteController instance.
     *
     * @return void
     */
    public function Quotation(Request $request)
    {
        $total = 0;
        
        $data = $request->validate([
            'age' => 'required',
            'currency_id' => 'required|in:EUR,USD,GBP',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        $ageRanges = config('age_range');

        $startDateTime = DateTime::createFromFormat('Y-m-d', $data['start_date']);
        $endDateTime = DateTime::createFromFormat('Y-m-d', $data['end_date']);

        
        $interval = $startDateTime->diff($endDateTime);

        $totalOfDays = $interval->days + 1;

        $ageArray = explode(",", $data['age']);
      
        foreach ( $ageArray as $age) {
            foreach ($ageRanges as $range) {
                if ($age >= $range['min_age'] && $age <= $range['max_age']) {
                    $weight = $range['weight'];
                    $total += (3 * $weight * $totalOfDays);
                    break; 
                }
            }
        }


        $user = Auth::user();

        $quote = new Quote();
        $quote->age = $data['age'];
        $quote->currency_id = $data['currency_id'];
        $quote->start_date = $data['start_date'];
        $quote->end_date = $data['end_date'];
        $quote->user_id = $user->id;

        // Save the quote to the database
        try {
            $quote->save();

            $res = [
                'total' => ceil($total),
                'currency_id' => $data['currency_id'],
                'quotation_id' => $quote->id
            ];
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json($res);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Quote::query()->where('user_id', Auth::id())->paginate(10);

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($quote_id)
    {
        $data = Quote::where('id', $quote_id)
        ->where('user_id', Auth::id())
        ->first();

        if (!$data) {
            return response()->json(['error' => 'Quote not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        //
    }
}
