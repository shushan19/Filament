<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;


use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Set;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected  static ?string $navigationLabel = 'Employee';

    protected static ?string $modelLabel = 'Employee';

    protected static ?string $navigationGroup = 'Employee Management';

    protected static ?string $recordTitleAttribute = 'fist_name';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->first_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'middle_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Last Name'=> $record->last_name,
            'Country'=> $record->country->name,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 0 ? 'info' : 'warning';
    }

//    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Name')
                ->description('Put the user details in ')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->maxLength(255),
                ])->columns(3),
                Forms\Components\Section::make('Foreign Id\'s')
            ->description('Put the foreign id\'s in ')
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->relationship('country', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
//                    ->afterStateUpdated(fn(Set $set) => $set('state_id',null)-> set('city_id',null))
                        ->afterStateUpdated(function (Set $set)
                    {
                        $set('state_id',null);
                        $set('city_id',null);
                })
                    ->required(),

                Forms\Components\Select::make('state_id')
                    ->label('State')
                    ->options(fn(Get $get): Collection => State::query()
                    ->where('country_id', $get('country_id'))
                    ->pluck('name','id'))
                    ->live()
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->afterStateUpdated(fn(Set $set)=> $set('city_id', null))
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(fn(Get $get): Collection => City::query()
                    ->where('state_id', $get('state_id'))
                    ->pluck('name','id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('User Address')
            ->schema([
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip_code')
                    ->required()
                    ->maxLength(255),
            ])->columns(2),

                Forms\Components\Section::make('Dates')
            ->schema([
                Forms\Components\DatePicker::make('birth_of_date')
                    ->native(false)
                    ->required(),
                Forms\Components\DatePicker::make('date_hired')
                    ->native(false)
                ->required(),
            ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_of_date')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Department')
                ->relationship('department',titleAttribute:"name")
                ->searchable()
                ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->native(false),
                        DatePicker::make('created_until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] && ! $data['created_until']) {
                            return null;
                        }

                        return 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString() .
                            ' to ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Employyee Deleted Successfully')
                )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Address Info')
                    ->schema([
                        TextEntry::make('country.name')->label('Country Name'),
                        TextEntry::make('state.name')->label('State Name'),
                        TextEntry::make('city.name')->label('City Name'),
                        TextEntry::make('department.name')->label('Department Name'),
                    ])->columns(2),
             Section::make('Basic Info')
                 ->schema([
                     TextEntry::make('first_name')->label('First Name'),
                     TextEntry::make('last_name')->label('Last Name'),
                     TextEntry::make('middle_name')->label('Middle Name'),
                 ])->columns(3),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
//            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
