<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\SubCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryPage extends SearchableComponent
{
    public $slug;

    public $subName;

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public $paginationTheme = 'bootstrap';
    public $perPage = 10;
    // public string $pageName = 'category-page';
    public array $numberOfPaginatorsRendered = [];
    
    protected $queryString = ['perPage'];
    
    public function updatingPerPage()
    {
        $this->resetPage();
    }


    /**
     * @var mixed
     */
    private $subCategory;

    public function mount($slug = null, $subName = null)
    {
        $this->slug = $slug;
        $this->subName = $subName;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $categoryPosts = $this->postsData();
        if(getCurrentTheme() == 1){
        return view('livewire.category-page-tailwind', compact('categoryPosts'));
        }
        return view('livewire.category-page', compact('categoryPosts'));
    }

    public function postsData(): LengthAwarePaginator
    {
        $this->setQuery(
            $this->getQuery()
                ->with(['category', 'postArticle', 'postGalleries', 'postSortLists.media', 'postSortLists', 'media', 'user'])
                ->where('visibility', Post::VISIBILITY_ACTIVE)
                ->orderByDesc('created_at')
        );

        $categoryId = Category::where('slug', $this->slug)->value('id');
        if ($categoryId) {
            $this->getQuery()->where('category_id', $categoryId);
        }

        if (!empty($this->subName)) {
            $subId = SubCategory::where('slug', $this->subName)->value('id');
            if ($subId) {
                $this->getQuery()->where('sub_category_id', $subId);
            }
        }

        return $this->getQuery()->paginate($this->perPage); // 👈 yahan fix hai
    }


    public function model()
    {
        return Post::class;
    }

    public function searchableFields()
    {
        return ['category_id'];
    }
}
