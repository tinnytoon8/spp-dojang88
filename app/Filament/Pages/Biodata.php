<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\Delete;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class Biodata extends Page
{   
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.biodata';

    public $user;

    public ?array $data = [];

    public function mount(): void {

        $this->user = Auth::user();

        // Inisiasi form dengan current data
        $this->form->fill([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'image' => $this->user->image,
            'certificate' => $this->user->certificate
        ]);
    }

    public function form(Form $form): Form {

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->required()->email(),
                        TextInput::make('password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->nullable(),
                        TextInput::make('phone')->required(),
                        FileUpload::make('image')->required()->image()->columnSpanFull(),
                        FileUpload::make('certificate')->required()->image()->columnSpanFull(),
                    ])
                ])->statePath('data');
            
    }

    public function edit(): void {

        // Validate from data
        $validatedData = $this->form->getState();

        // update the user's details
        $this->user->name = $validatedData['name'];
        $this->user->email = $validatedData['email'];
        $this->user->phone = $validatedData['phone'];

        // update password if provided
        if(!empty($validatedData['password'])){
            $this->user->password = Hash::make($validatedData['password']);
        }

        // handle image upload
        if(isset($validatedData['image'])) {
            if($this->user->image) {
                Storage::Delete($this->user->image);
            }
            $this->user->image = $validatedData['image'];
        }

        // handle scan certificate upload
        if(isset($validatedData['certificate'])) {
            if($this->user->certificate) {
                Storage::Delete($this->user->certificate);
            }
            $this->user->certificate = $validatedData['certificate'];
        }

        $this->user->save();

        // send a success notification
        Notification::make()
            ->title('Biodate Updated')
            ->success()
            ->body('Your biodata has been successfully updated.')
            ->send();
    }
}
