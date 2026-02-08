<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\RattingsImport;

class RattingsImportController extends Controller
{
    public function showForm()
    {
        return view('admin.rattings.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new RattingsImport();
            app('excel')->import($import, $request->file('file'));

            $errors = $import->getErrors();
            $created = $import->getCreatedCount();

            if (!empty($errors)) {
                // show simple messages to non-technical users
                return redirect()->route('rattings.import.form')
                    ->with('import_errors', $errors)
                    ->with('success', "Import selesai. Berhasil menambah {$created} data. Beberapa baris dilewati.");
            }

            return redirect()->route('rattings.index')->with('success', 'Import rattings berhasil.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        }
    }
}
