<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Exports\StudentExport;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    #--------------------------------------------------------------------------#
    # TODO     : Display a listing of the resource.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : index
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function index()
    {
        $student = Student::all();
        return view('index', compact('student'));
    }

    #--------------------------------------------------------------------------#
    # TODO     : Show the form for creating a new resource.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : create
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function create()
    {
        return view('create');
    }

    #--------------------------------------------------------------------------#
    # TODO     : Store a newly created resource in storage.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : store
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|numeric',
            'image_base64' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['name'] = $request->name;
        $input['email'] = $request->email;
        $input['phone'] = $request->phone;
        $input['photo'] = $this->storeBase64($request->image_base64);
        $student = Student::create($input);
        if ($student) {
            return redirect('/students')->with('success', 'Student has been saved!');
        }
        return redirect('/students/create')->with('success', 'Somthing went wrong. Try again');
    }

    #--------------------------------------------------------------------------#
    # TODO     : image upload
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : storeBase64
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function storeBase64($imageBase64)
    {
        list($type, $imageBase64) = explode(';', $imageBase64);
        list(, $imageBase64)      = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);
        $imageName = time() . '.png';
        $path = public_path() . "/Images/" . $imageName;

        file_put_contents($path, $imageBase64);

        return $imageName;
    }

    #--------------------------------------------------------------------------#
    # TODO     : Display the specified resource.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : show
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function show($id)
    {
        //
    }

    #--------------------------------------------------------------------------#
    # TODO     : Show the form for editing the specified resource.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : edit
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('edit', compact('student'));
    }

    #--------------------------------------------------------------------------#
    # TODO     : Update the specified resource in storage.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : update
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function update(Request $request, $studentId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['name'] = $request->name;
        $input['email'] = $request->email;
        $input['phone'] = $request->phone;
        if ($request->image_base64) {
            $input['photo'] = $this->storeBase64($request->image_base64);
        }
        $student = Student::where('student_id', $studentId)->update($input);
        if ($student) {
            return redirect('/students')->with('success', 'Student has been updated');
        }
        return redirect('/students')->with('success', 'Somthing went wrong. Try again');
    }

    #--------------------------------------------------------------------------#
    # TODO     : Remove the specified resource from storage.
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : update
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function destroy($studentId)
    {
        $student = Student::where('student_id', $studentId)->delete();
        if ($student) {
            return redirect('/students')->with('success', 'Student has been deleted');
        }
        return redirect('/students')->with('success', 'Somthing went wrong. Try again');
    }

    #--------------------------------------------------------------------------#
    # TODO     : Export data of excel
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : export
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function export()
    {
        return Excel::download(new StudentExport, 'students.xlsx');
    }

    #--------------------------------------------------------------------------#
    # TODO     : Import data of excel
    # DEV's    : Bhavit
    # DATE     : 01-07-2023
    # Method   : Import
    # Status   : [InUse]
    #--------------------------------------------------------------------------#
    public function import()
    {
        Excel::import(new StudentImport, request()->file('file'));
        return back();
    }
}
