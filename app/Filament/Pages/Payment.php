<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class Payment extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.payment';

    public $transaction;
    public ?array $data = []; //properti untuk menampung data formulir

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Menyembunyikan dari sidebar
    }

    public function mount(int $id): void
    {
        // Ambil transaksi berdasarkan ID
        $this->transaction = Transaction::findOrFail($id);

        // Isi data awal formulir berdasarkan transaksi
        $this->data = [
            'payment_method' => $this->transaction->payment_method ?? null,
            'payment_proof' => $this->transaction->payment_proof ?? null,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                    ])
                    ->required()
                    ->default($this->data['payment_method']), // Menggunakan data awal

            FileUpload::make('payment_proof')
                ->label('Bukti Pembayaran')
                ->image()
                ->required()
                ->directory('payment_proofs') // Menentukan direktori penyimpanan
                ->columnSpanFull(),
        ])->statePath('data'); // Mengikat data ke properti $data
    }

    public function edit()
    {
        // Validasi data
        $validatedData = $this->form->getState();

        // Hapus file lama jika file baru diunggah
        if (isset($validatedData['payment_proof']) && $validatedData['payment_proof'] !== $this->transaction->payment_proof) {
            if ($this->transaction->payment_proof) {
                Storage::delete($this->transaction->payment_proof);
            }
        }

        // Update transaksi
        $this->transaction->update([
            'payment_method' => $validatedData['payment_method'],
            'payment_proof' => $validatedData['payment_proof']
        ]);

        // Kirim notifikasi
        Notification::make()
            ->title('Pembayaran Berhasil!')
            ->body('Terima Kasih Telah Membayar Mohon Tunggu Persetujuan Oleh Admin')
            ->success()
            ->send();

        // Redirect ke halaman admin
        return redirect('/admin');
    }

}
