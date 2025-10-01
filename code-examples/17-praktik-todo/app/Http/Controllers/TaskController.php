<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua tasks, urutkan dari yang terbaru
        $tasks = Task::latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // Default is_completed = false
        $validated['is_completed'] = false;

        // Simpan ke database
        Task::create($validated);

        // Redirect dengan success message
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil ditambahkan! 🎉');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // Update task
        $task->update($validated);

        // Redirect dengan success message
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil diupdate! ✅');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();

            return redirect()->route('tasks.index')
                             ->with('success', 'Task berhasil dihapus! 🗑️');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Gagal menghapus task: ' . $e->getMessage());
        }
    }

    /**
     * Toggle completed status
     */
    public function toggle(Task $task)
    {
        // Toggle is_completed (true -> false, false -> true)
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        $status = $task->is_completed ? 'completed' : 'uncompleted';

        return redirect()->route('tasks.index')
                         ->with('success', "Task marked as {$status}!");
    }
}
