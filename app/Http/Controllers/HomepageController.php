<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\doctor;
use App\Models\User;
use App\Models\Time;
use App\Models\Appointment;
use DateTime;
use Carbon\CarbonPeriod;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;

class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('reserve.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$dateTime = Carbon::parse($request->your_datetime_field);
        //$request['your_datetime_field'] = $dateTime->format('Y-m-d H:i:s');
        //$("input").datepicker({ dateFormat: 'dd, mm, yy' });
        //store booking user
        // $date = $request->input('dateFrom');
        // $data = $request->all();
        // $data['transaction_date'] = Carbon::createFromFormat('m/d/Y', $request->transaction_date)->format('Y-m-d');
        // $transaction = Transaction::create($data);
        // $date=$request->input('date');
        // $date=$request->input('datepicker');
        // $date['date']=Carbon::createFromFormat('d/m/Y',$request->date)->format('Y-m-d');
        // $date_variable = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');
        // $date=Carbon::createFromFormat('d-m-y',$request->date->format('Y-m-d'));
        $request->validate(['time'=>'required']);
        // $check=$this->checkBookingTimeInterval();
        // if($check){
        //     return redirect()->back()->with('errmsg','You have already booked an appointment');
        // }
        $date=Carbon::parse($request->dateText);
        $time=Carbon::parse($request->time); 
        Appointment::create([
            'user_id'=>auth()->user()->id,
            'doctor_id'=>$request->doctorId,
            'time'=>$time,
            'date'=>$date,
            'status'=>0,
        ]);
        //send email notification
        $appointment=Appointment::all();
        $notificationBooking=[
            'name'=>auth()->user()->name,
            'time'=>$time,
            'date'=>$date,
            'doctor_name'=>request()->get('doctorName'),
            'cli_name'=>request()->get('clinicName'),
            'doctor_add1'=>request()->get('docAdd1'),
            'doctor_add2'=>request()->get('docAdd2'),
            'doctor_add3'=>request()->get('docAdd3'),
            'doctor_add4'=>request()->get('docAdd4'),
            'doctor_postcode'=>request()->get('docPostcode'),
            'doctor_state'=>request()->get('docState')
            
        ];
        Mail::to(auth()->user()->email)->send(new \App\Mail\Notification($notificationBooking));
        return redirect()->back()->with('msg','Your appointment was booked');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($doctorId)
    {

        $doctor=doctor::where('id',$doctorId)->first();
        $doctor_id=$doctorId;
        return view('reserve.appointment',compact('doctor','doctor_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function search(Request $request){
            $keyword=$request->get('keyword');
            $cli_name=$request->get('clinic');
            $address=$request->get('add');
            $services=$request->get('service');
            //get all data from db
            $reserves=doctor::all();
            if($keyword){
                $reserves=doctor::where("doc_service","LIKE","%".$keyword."%")
                ->orWhere("cli_name","LIKE","%".$keyword."%")
                ->orWhere("doc_specialist","LIKE","%".$keyword."%")
                ->get();
            }
            if($cli_name){
                $reserves=doctor::where('cli_name','LIKE','%'.$cli_name.'%')->get();
            }
            if($services){
                $reserves=doctor::where('doc_service','LIKE','%'.$services.'%')->get();
            }
            if($address){
                $reserves=doctor::where('doc_address1','LIKE','%'.$address.'%')
                ->orWhere("doc_address2","LIKE","%".$address."%")
                ->orWhere("doc_address3","LIKE","%".$address."%")
                ->orWhere("doc_address4","LIKE","%".$address."%")
                ->orWhere("doc_postcode","LIKE","%".$address."%")
                ->orWhere("doc_state","LIKE","%".$address."%")
                ->get();
            }
            return view('reserve.search',compact('reserves'));
        }
        public function timeSlot(Request $request){
            // $time=new CarbonPeriod('08:00','15 minutes','13:00');
            // $slots=[];
            // foreach($time as $item){
            //     array_push($slots,$item->format("h:i A"));
            // }

            // $dates=[];
            // $slots=$start_time->diffInMinutes($end_time)/$interval;

            // $dates[$start_time->toDateString()][]=$start_time->toTimeString();
            // for($i=1;$i<=$slots;$i++){
            //     $dates[$start_time->toDateString()][]=$start_time->addMinute($interval)->toTimeString();

            // }
            // dd($dates);

            // $start_time=Carbon::now();
            // $newDateTime=Carbon::now()->addMinutes(5);
            // return view('reserve.appointment',compact('dates'));
            
            // 1. get request from db $start_time & $end_time & interval(duration each appointment)
            // 2. change to time format data from request 
            // 3. while loop to add interval on start time and stop before end_time
            // 4. return view time

            // $doctor=$request->get('doctorId');
            // $times=doctor::where('id',$doctor)->orWhere('start_time',$request->$start_time)->first();
            // dd($times);

            // $result = DB::table('users')->select('groupName')->where('username', $username)->first();
            // return $result->groupName; 

            // $time=doctor::all();
            // $start_time={{ $time->start_time }};
            // $start_times=$request->get('doc_start_time');
            // return view('reserve.appointment',compact('start_times'));
        }
        public function myBooking(){
            $appointments = Appointment::latest()->where('user_id',auth()->user()->id)->get();
            // $appointments=Appointment::all();
            return view('booking.bookingDetails',compact('appointments'));
        }
        public function BookingDetails($appointmentsId){
            $appointments=Appointment::where('id',$appointmentsId)->first();
            $appointments_id=$appointmentsId;
            return view('booking.index',compact('appointments','appointments_id'));
        }

        public function checkBookingTimeInterval()
        {
            return Appointment::orderby('id','desc')
                ->where('user_id',auth()->user()->id)
                ->whereDate('created_at',date('Y-m-d'))
                ->exists();
        }

        
}
