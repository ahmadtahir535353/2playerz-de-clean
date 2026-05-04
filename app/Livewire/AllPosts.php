<?php

namespace App\Livewire;

use App\Models\Post;

class AllPosts extends SearchableComponent
{
    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public $paginationTheme = 'bootstrap';
    public $numberOfPaginatorsRendered = [];

    public $perPage = 10;
    public $search = ''; 
    public $time = '';
    public $category = '';
    public $subcategory = '';
    public $editor = '';

    protected $queryString = ['search', 'time', 'category', 'subcategory', 'editor', 'perPage'];

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $allPosts = $this->allPosts();

        if (getCurrentTheme() == 1) {
            return view('livewire.all-posts-tailwind', compact('allPosts'));
        }

        return view('livewire.all-posts', compact('allPosts'));
    }

    public function allPosts()
    {
        $query = Post::with(['user', 'category', 'postVideo'])
            ->where('visibility', Post::VISIBILITY_ACTIVE)->searchTitle($this->search);

        // Time filter
        if ($this->time === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->time === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->time === 'month') {
            $query->whereMonth('created_at', now()->month);
        }

        // Category filter
        if (!empty($this->category) && $this->category !== 'All') {
            $query->whereHas('category', function ($q) {
                $q->where('name', $this->category);
            });
        }

        // Subcategory filter
        if (!empty($this->subcategory) && $this->subcategory !== 'All') {
            $query->whereHas('subCategory', function ($q) {
                $q->where('name', $this->subcategory);
            });
        }

        // Editor filter
        if (!empty($this->editor) && $this->editor !== 'All') {
            $query->where('created_by', $this->editor);
        }

        $query->withCount('comment');

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function model()
    {
        return Post::class;
    }

    public function searchableFields()
    {
        return ['title']; // ✅ define sirf title
    }
}
