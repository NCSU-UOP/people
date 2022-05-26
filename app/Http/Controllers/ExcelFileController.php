<?php

namespace App\Http\Controllers;

use App\Models\ExcelDetails;
use App\Models\Faculty;
use App\Models\Batch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Imports\UsersImport;

class ExcelFileController extends Controller
{
    protected $messages = [
        'required' => 'The :attribute field is required.',
        'same' => 'The :attribute and :other must match.',
        'size' => 'The :attribute must be exactly :size.',
        'min' => 'The :attribute must be greater than :min characters.',
        'max' => 'The :attribute must be less than :max characters.',
        'between' => 'The :attribute value :input is not between :min - :max.',
        'in' => 'The :attribute must be one of the following types: :values',
        'unique' => 'The :attribute is already in use.',
        'exists' => 'The :attribute is invalid.',
        'regex' => 'The :attribute format is invalid.',
        'email' => 'Invalid email.',
        'string' => 'The :attribute should be a string.',
        'integer' => 'The :attribute field is required.',
        'confirmed' => 'Password and Confirm Password must be match',
        'mimes' => 'File format is invalid. only xlsx is supported.',
    ];

    //function to import excel file, $id should be given from the route as a parameter
    public function importExcelFile($id)
    {   
        $excel_details = ExcelDetails::where('id', $id)->first();
        $excel_file_attributes = $excel_details->attributes;
        $excel_filename = $excel_details->excel_filename;
        $usertype = $excel_details->usertype;
        $admin_id = $excel_details->admin_id;
        $batch_id = $excel_details->batch_id;
        // $department_id = $excel_details->department_id;
        $faculty_id = $excel_details->faculty_id;


        try {
            $import = new UsersImport($faculty_id,$batch_id,$usertype,$id);
            $import->import(public_path('/uploads/excelfiles/'.$excel_filename.'.xlsx'));
            $excel_details->is_imported = true;
            $excel_details->save();
            return redirect()->back()->with('success', 'Excel file imported!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            return redirect()->back()->with('Error', 'Excel file import terminated!')->with('failures',$failures);
        }

    }

    //function to delete excel file, $id should be given from the route as a parameter,
    //first go through each row of the excel file and delete if any user find in AD corresponding to the row
    //secondly remove the excel file importations from user table by quering the user table with the excel file id -> this should delete the cascaded entries in the student table too
    //finally remove the excel file from the public folder and corresponding entry is deleted from excel_details table 
    public function removeExcelFile($id)
    {   

    }

    // (Super Admin) add excel file
    public function addExcelFile() 
    {
        $faculty = Faculty::select('id', 'name')->get();
        $batch = Batch::select('id')->get();
        $usertype = [[env('STUDENT'),'Student'], [env('ACADEMIC_STAFF'),'Academic Staff'], [env('NON_ACADEMIC_STAFF'),'Non-Academic Staff']];
        // dd($usertype['1']);
        return view('admin.uploadExcel', compact('faculty','batch','usertype'));
    }

    // upload excel file POST method
    public function uploadExcelFile() 
    {
        $usertypes = [env('STUDENT'),env('ACADEMIC_STAFF'),env('NON_ACADEMIC_STAFF')];
        $Data = request()->validate([
            'usertype' => ['required','integer',Rule::in([env('STUDENT'),env('ACADEMIC_STAFF'),env('NON_ACADEMIC_STAFF')])],
            'faculty_id' => ['required','int','exists:faculties,id'],
            'batch_id' => ['int','exists:batches,id'],
            'excel_file' => ['required', 'file', 'mimes:xlsx', 'max:2048'],
            'excelAttributes' => ['array'],
        ], $this->messages);

        dd($Data);
        //!TODO should implement storing the excel file in public folder and storing the details in the database
        // dd($adminData);

        // return redirect('/dashboard')->with('message', 'User has been created Succesfully 👍');
    }
}
