<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $perPage = 20;

    public function getFilenameAttribute()
    {
        return md5($this->getKey());
    }

    public function getThumbnailUrlAttribute()
    {
        $disk = Storage::disk('public');

        $thumb = $disk->path("thumbs/{$this->filename}.png");

        if (! File::exists($thumb))
        {
            if (! File::isDirectory('thumbs'))
            {
                $disk->makeDirectory('thumbs');
            }

            $image = new \Imagick($disk->path("documents/{$this->filename}.pdf[0]"));
            $image->setImageFormat('png');
            $image->thumbnailImage(config('documents.thumb_width', 500), 0);

            $image->writeImage($thumb);
        }

        return asset("storage/thumbs/{$this->filename}.png");
    }

    public function getDocumentUrlAttribute()
    {
        return asset("storage/documents/{$this->filename}.pdf");
    }

    public function delete()
    {
        Storage::disk('public')->delete([
            "documents/{$this->filename}.pdf",
            "thumbs/{$this->filename}.png",
        ]);

        return parent::delete();
    }

    public function toArray()
    {
        //extend toArray method, no need to create a resource
        return parent::toArray() + [
            'thumbnail' => $this->thumbnail_url,
            'show_url' => route('documents.show', $this->getKey()),
            'pdf' => $this->document_url
        ];
    }
}
