<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SecuredFile extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Document
     */
    private $document;

    /**
     * @var string|string
     */
    private $partyUuid;

    /**
     * @var string|string
     */
    private $receiversName;

    /**
     * @var string
     */
    private $securedFolderName = 'secured';

    /**
     * SecuredFile constructor.
     * @param Document $document
     * @param string $partyUuid
     * @param string $receiversName
     */
    public function __construct(Document $document, string $partyUuid, string $receiversName)
    {
        $this->document = $document;
        $this->receiversName = $receiversName;
        $this->partyUuid = $partyUuid;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $folderName = 'secured';

        $file = Storage::disk('local')->path(
            $this->partyUuid.
            DIRECTORY_SEPARATOR.
            $folderName.
            DIRECTORY_SEPARATOR.
            $this->document->uuid.
            DIRECTORY_SEPARATOR.
            $this->document->slug_name
        );

        return $this->view('emails.secured_file_mail')
            ->with(['documentName' => $this->document->slug_name, 'name' => $this->receiversName])
            ->attach($file);
    }
}
