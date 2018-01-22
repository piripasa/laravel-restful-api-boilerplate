<?php namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleRepository
{
	private $article;

	public function __construct (Article $article) {
		$this->article = $article;
	}

	public function generateArticleSlug ($string) {
		return sprintf("%s-%s", substr(str_slug($string), 0, 39), str_random(10));
	}

	public function storeArticle ($articleData, $userId) {
		$article = $this->article->newInstance();
		foreach ( $articleData as $key => $value ) {
			$article->{$key} = $value;
		}
		$article->slug = $this->generateArticleSlug($articleData['title']);
		$article->user_id = $userId;
		$article->save();

		return $article;
	}

	public function updateArticleChange (Article $article, $articleData) {
		foreach ( $articleData as $field => $value ) {
			$article->{$field} = $value;
			if ($field == 'title') {
				$article->slug = $this->generateArticleSlug($value);
			}
		}
		$article->save();

		return $article;
	}

	public function insertArticleTagToPivot (Article $article, Collection $tags) {
		return $article->tags()->attach($tags);
	}

	public function updateArticleTagsToPivotTable (Article $article, Collection $tags) {
		return $article->tags()->sync($tags);
	}

	public function fetchAnArticleBySlug ($slug, $relations = []) {
		// making sure relations are provided as array tho as parameters work
		if ( !is_array($relations)) {
			$relations = [ $relations ];
		}

		return $this->article->with($relations)->where('slug', $slug)->first();
	}

	public function getChunkOfArticles ($perPage, $searchQuery) {
		return $this->article->setPerPage($perPage)
							 ->search($searchQuery)
							 ->with('user', 'tags')
							 ->paginate()
							 ->appends([ 'per_page' => $this->article->getPerPage(), 'search' => $searchQuery ]);
	}

	public function getChunkOfArticlesByUser ($userId, $perPage, $searchQuery) {
		return $this->article->setPerPage($perPage)
							 ->where('user_id', $userId)
							 ->where(function ($query) use ($searchQuery) {
								 $query->search($searchQuery);
							 })
							 ->with('user', 'tags')
							 ->paginate()
							 ->appends([ 'per_page' => $this->article->getPerPage(), 'search' => $searchQuery ]);
	}

	public function getChunkOfArticlesByTag ($tagId, $perPage, $searchQuery) {
		return $this->article->setPerPage($perPage)->whereHas('tags', function ($query) use ($tagId) {
			$query->where('tag_id', $tagId);
		})->where(function ($query) use ($searchQuery) {
			$query->search($searchQuery);
		})->with('user', 'tags')->paginate()->appends([
			'per_page' => $this->article->getPerPage(),
			'search'   => $searchQuery,
		]);
	}

	public function deleteAnArticle (Article $article) {
		return $article->delete();
	}
}