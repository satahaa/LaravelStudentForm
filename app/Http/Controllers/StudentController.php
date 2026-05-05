<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('student');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255|unique:students,student_id',
            'email' => 'required|email|max:255|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:255',
        ], [
            'student_id.unique' => 'This Student ID is already registered.',
        ]);

        Student::create($validated);

        return response()->json(['message' => 'Student registered successfully!']);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $students = Student::where('student_id', 'like', "%{$query}%")
                           ->orWhere('name', 'like', "%{$query}%")
                           ->limit(10)
                           ->get();

        return response()->json($students);
    }
}
