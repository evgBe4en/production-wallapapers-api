<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class FirebaseController extends Controller
{
    private $database;
    private $storage;

    public function __construct()
    {
        $this->database = FirebaseService::database();
        $this->storage = FirebaseService::storage();
    }
    public function index()
    {
        return response()->json($this->database->getReference('/')->getValue());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create-image');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $image = $request->file('image');

        $imageName = (string) Str::uuid();
        $pathName = $image->getPathname();
        $file = fopen($pathName, 'r');
        $object = $this->storage->getBucket()->upload($file, [
            'name' => $imageName . '.webp',
            'contentType' => 'image/webp',
            'predefinedAcl' => 'publicRead',
            'public' => true,
            'acl' => []
        ]);

        $date = new \DateTime('tomorrow');
        $date->setDate(2024, 1, 1);

        $image_url = $object->signedUrl($date);


        $this->database
            ->getReference('wallpapers/' . uuid_create())
            ->set([
                'name' => $request['name'] ,
                'image_url' => $image_url,
                'category' => $request['category']
            ]);

        return response()->json('wallpaper has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
