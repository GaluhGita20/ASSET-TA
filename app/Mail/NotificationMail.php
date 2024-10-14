<?php

namespace App\Mail;

use App\Models\Globals\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    // Menggunakan trait Queueable memungkinkan email untuk dikirim dalam antrian (queue), yang berguna untuk pengiriman email secara asinkron.
    // Trait SerializesModels digunakan untuk memastikan bahwa model yang ada dalam email diserialisasi dengan benar saat ditransmisikan melalui antrian.

    public $record;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(Notification $record)
    {
        $this->record  = $record;
        $this->subject = $record->show_module.' - '.$record->show_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('mails.notification')
                    ->with([
                        'record' => $this->record
                    ]);
    }

    //  Metode ini bertanggung jawab untuk membangun konten email.
    // Di dalamnya, subject($this->subject) mengatur subjek email.
    // view('mails.notification') menentukan tampilan (view) yang akan digunakan untuk konten email. Tampilan ini biasanya berisi HTML yang dirender sebagai isi email.

    // with(['record' => $this->record]) menyampaikan data ke tampilan email. Dalam hal ini, objek $record yang telah disimpan sebelumnya akan tersedia dalam tampilan.

}
