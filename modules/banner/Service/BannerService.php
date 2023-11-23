<?php

namespace Banner\Service;

use Aws\Exception\AwsException;
use Banner\Enumerators\S3Path;
use Banner\Exceptions\ErrorSavingImages;
use Banner\Models\Banner;
use Banner\Repositories\BannerRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerService
{
    protected $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findByCredentials(string $client, string $secret)
    {
        return $this->repository->findByCredentials($client, $secret);
    }

    public function bulkEdit(array $banners): Collection
    {
        $bulk = collect();
        foreach ($banners as $banner) {
            $id = data_get($banner, 'id');
            $bulk->push($this->edit($id, $banner));
        }
        return $bulk;
    }

    public function edit($id, array $attributes)
    {
        if (filled($attributes)) {
            $banner           = new Banner();
            $startAt          = data_get($attributes, 'startAt');
            $endAt            = data_get($attributes, 'endAt');
            $banner->order    = data_get($attributes, 'order');
            $banner->href     = data_get($attributes, 'href', '');
            $banner->start_at = filled($startAt) ? \Carbon\Carbon::parse($startAt) : null;
            $banner->end_at   = filled($endAt) ? \Carbon\Carbon::parse($endAt) : null;
            $attributes       = array_filter(get_object_vars($banner));
            return $this->repository->edit($id, $attributes);
        }
        return null;
    }

    public function store(array $attributes): ?Banner
    {
        $label        = data_get($attributes, 'label');
        $slug         = str_slug($label) . explode('.', microtime(true))[0];
        $imageDesktop = data_get($attributes, 'imageDesktop');
        $imageMobile  = data_get($attributes, 'imageMobile');
        $imageTablet  = data_get($attributes, 'imageTablet');

        $imagesDesktop = $this->generatePlaceholder($imageDesktop, $slug, 'desktop');
        $imagesMobile  = $this->generatePlaceholder($imageMobile, $slug, 'mobile');
        $imagesTablet  = $this->generatePlaceholder($imageTablet, $slug, 'tablet');

        $startAt               = data_get($attributes, 'startAt');
        $endAt                 = data_get($attributes, 'endAt');
        $banner                = new Banner();
        $banner->slug          = $slug;
        $banner->order         = data_get($attributes, 'order');
        $banner->href          = data_get($attributes, 'href');
        $banner->key           = data_get($attributes, 'key');
        $banner->start_at      = \Carbon\Carbon::parse($startAt);
        $banner->end_at        = \Carbon\Carbon::parse($endAt);
        $banner->image_desktop = $imagesDesktop[0];
        $banner->image_tablet  = $imagesTablet[0];
        $banner->image_mobile  = $imagesMobile[0];

        $banner->image_desktop_placeholder = $imagesDesktop[1];
        $banner->image_tablet_placeholder  = $imagesTablet[1];
        $banner->image_mobile_placeholder  = $imagesMobile[1];
        $banner->validate();

        return $this->repository->save($banner);
    }

    public function generatePlaceholder(UploadedFile $file, $slug, $type = ''): array
    {
        $extension = explode('/', $file->getClientMimeType())[1];
        $name      = $slug . "_{$type}." . $extension;

        $placeholder = Image::make($file->getRealPath());
        $width       = $placeholder->width();
        $height      = $placeholder->height();

        $placeholder = $placeholder->resize($width / 10, $height / 10)->encode($extension);

        try {
            $path = S3Path::BANNER_DIR . "/{$slug}/placeholder_{$name}";
            Storage::disk('s3')->put($path, $placeholder->__toString(), 'public');
            $placeholderPath = Storage::disk('s3')->url($path);

            $path = S3Path::BANNER_DIR . "/{$slug}/{$name}";
            Storage::disk('s3')->put($path, file_get_contents($file), 'public');
            $imagePath = Storage::disk('s3')->url($path);
            return [$imagePath, $placeholderPath];
        } catch (AwsException $exception) {
            throw new ErrorSavingImages($exception->getMessage());
        }
    }

    public function getAll($key)
    {
        return $this->repository->getByKey($key)
            ->where('start_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->shuffle();
    }

    public function getAllAdmin($key)
    {
        return $this->repository->getByKey($key);
    }

    public function show($id)
    {
        return $this->repository->find($id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
}
