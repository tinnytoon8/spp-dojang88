<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Pages\Auth\Register as AuthRegister;

class Register extends AuthRegister {

    protected function getForms(): array {
        return [
            'form' => $this->form(
                $this->makeForm()
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                    TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->label('Phone Number')
                        ->placeholder('Enter your phone number'),
                    FileUpload::make('image')
                        ->label('Image Profile')
                        ->columnSpanFull()
                        ->required()
                        ->image()
                        ->placeholder('Upload your profile picture'),
                    FileUpload::make('certificate')
                        ->label('Scan of certificate')
                        ->columnSpanFull()
                        ->image()
                        ->placeholder('Upload your last certificate'),
                ])
                ->statePath('data'),
            )
        ];
    }

    protected function submit(): void {

        $data = $this->form->getState();

        $user = User::Create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['name']),
            'phone' => $data['phone'],
            'image' => $data['image'] ?? null,
            'certificate' => $data['certificate'] ?? null,
        ]);

        Auth::login($user);
    }
}