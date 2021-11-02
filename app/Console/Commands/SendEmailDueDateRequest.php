<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\Services\Api\RequestServiceInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\DueDateRequestEmail;

class SendEmailDueDateRequest extends Command
{
    protected $requestService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail_due_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RequestServiceInterface $requestService)
    {
        parent::__construct();
        $this->requestService = $requestService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requests = $this->requestService->getRequestDueDate();
        if (!empty($requests[0])) {
            foreach ($requests as $item) {
                Mail::to($item->manager->email)->send(new DueDateRequestEmail($item));
            }
        }
    }
}
