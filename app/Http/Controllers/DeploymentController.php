<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DeploymentController extends Controller
{
    /**
     * Create the storage symlink manually to bypass shell restrictions.
     */
    public function linkStorage()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (file_exists($link)) {
            return "The [public/storage] link already exists.";
        }

        if (!file_exists($target)) {
            return "The target directory [storage/app/public] does not exist.";
        }

        // Attempt native PHP symlink
        try {
            if (!function_exists('symlink')) {
                throw new \Exception("Function 'symlink' is disabled or undefined in this PHP environment.");
            }
            \symlink($target, $link);
            return "Successfully created symlink from [$target] to [$link].";
        } catch (\Throwable $e) {
            return "Failed to create symlink: " . $e->getMessage();
        }
    }
}
