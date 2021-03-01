<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentsCollection;
use App\Models\Document;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        foreach (Document::all() as $document)
//        {
//            $document->delete();
//        }

        return response()->json(Document::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => ['file', 'mimes:pdf']
        ]);

        $file = $request->file('document');

        //create the model first
        $document = Document::create([
            'name' => $file->getClientOriginalName()
        ]);

        //then use its ID to create a filename
        $file->storeAs('documents',  "{$document->filename}.pdf", 'public');

        try
        {
            //it will create a thumbnail from PDF, potential imagick exception
            //I would've moved this code to a background job, but we need to push the result on frontend to the end of the list
            $documentArray = $document->toArray();
        }
        catch (\ImagickException $e)
        {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($documentArray);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return response()->json($document->toArray());
    }
}
