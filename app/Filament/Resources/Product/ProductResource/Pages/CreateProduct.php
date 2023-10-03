<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Models\Filter\FilterGroup;
use App\Models\Product\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Livewire\Component as Livewire;


class CreateProduct extends Page
{
    use InteractsWithFormActions;

    protected static string $resource = ProductResource::class;
    public ?array $data = [];
    public ?string $type = null;
    public int $step = 1;
    public bool $isContinue = false;
    protected static string $view = 'filament.custom.default.resources.product.pages.product-create';

    /**
     * Get Current Form State & Data
     * @return string
     */
    protected function getFormStatePath(): string
    {
        return 'data';
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('Create')->action('create')->label(function (){
                return ($this->step == 1 && $this->isContinue) ? 'Continue' : 'Create';
            })->color(function (){
                return ($this->step == 1 && $this->isContinue) ? 'primary' : 'success';
            }),
        ];
    }

    /**
     * @return array
     */
    protected function configSchema(): array
    {
        return (empty($this->data['filter_group_id'])) ? [] : $this->getFilterDetails($this->data['filter_group_id']);
    }


    /**
     * @param int $id
     * @return array
     */
    private function getFilterDetails(int $id): array
    {
        $group = FilterGroup::where('id', $id)->with('filters.options')->first();


        return $group->filters->map(function ($item, $key) {
            $optionBag = $item->options->mapWithKeys(function ($item, $key) {
                return [$item['admin_name'] => $item['admin_name']];
            })->toArray();
            return Select::make('filter_attributes.'.$item->admin_name)->options($optionBag)->multiple()->required();
        })->toArray();
    }


    public  function getFormSchema(): array
    {
        return [

            Section::make('Product Details')
                ->schema([
                    Select::make('type')
                        ->options(Product::TYPE_OPTION)
                        ->lazy()
                        ->afterStateUpdated(function ($state){
                            $this->isContinue = $state == Product::CONFIGURABLE;
                        })
                        ->required(),
                    TextInput::make('name')->required()->helperText('product name to be displayed.'),
                    TextInput::make('sku')->label('SKU')
                        ->required()
                        ->unique(table: Product::class)
                        ->helperText('Stock Keeping Unit (SKU) is the unique id that will be assigned to your product.'),

                    Select::make('filter_group_id')
                        ->label('Filter Group')
                        ->options(FilterGroup::where('type', FilterGroup::FILTERABLE)->pluck('admin_name', 'id'))->required()->helperText('filters family adds a group of attributes to your product. (eg. color, size, material, medium)
                            choose the family according to your product type.'),
                ]),
            Section::make('Filter Details')
                ->description('Description')
                ->columns(2)
                ->schema($this->configSchema())
                ->visible(fn (Livewire $livewire): bool => $livewire->type == 'configurable'),
        ];
    }




    public function create(bool $another = false)
    {

        $formData = $this->form->getState();
        $this->type = $formData['type'];

        if ($formData['type'] == Product::SIMPLE)
        {
            return $this->createSimple($formData);
        }elseif ($formData['type'] == Product::CONFIGURABLE && $this->step == 1)
        {
            $this->type = $formData['type'];
            $this->form->fill($formData);
            $this->step = 2;

        }elseif ($formData['type'] == Product::CONFIGURABLE && $this->step == 2)
        {
            return $this->createConfigurable($formData);
        }else{
            $this->notify('danger','undefined product type selected');
            $this->halt();
        }


    }


    public function createSimple(array $data):RedirectResponse|Redirector
    {
        $typeInstance = app(config('project.product_types.'.$data['type'].'.class'));
        $product = $typeInstance->create($data);
        return redirect()->route('filament.admin.resources.product.edit', ['record' => $product->id]);

    }


    public function createConfigurable(array $data):RedirectResponse|Redirector
    {

        $typeInstance = app(config('project.product_types.'.$data['type'].'.class'));
        $product = $typeInstance->create($data);
        return redirect()->route('filament.resources.product.edit', $product);
    }





}
