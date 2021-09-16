<?php

namespace AAbosham\Filament\Forms\Components\Contracts;

use SplFileInfo;

interface HasFileAttachments
{
    public function saveUploadedFileAttachment(SplFileInfo $attachment): ?string;
}
