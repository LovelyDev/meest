<?php

namespace Extensions\CustomFileSessionHandler;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use SessionHandlerInterface;
use Symfony\Component\Finder\Finder;
use \Illuminate\Session\FileSessionHandler;

class CustomFileSessionHandler extends FileSessionHandler
{


    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->files->delete($this->path.'/'.$sessionId);

        return true;
    }

}
