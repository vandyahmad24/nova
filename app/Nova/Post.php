<?php

namespace App\Nova;

use App\Nova\Actions\PublishedPost;
use App\Nova\Actions\UnPublishedPost;
use App\Nova\Filters\PostCategory;
use App\Nova\Filters\PostPublished;
use App\Nova\Lenses\MostTags;
use App\Nova\Metrics\PostCount;
use App\Nova\Metrics\PostPerCategory;
use App\Nova\Metrics\PostPerday;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Post';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','title','body'
    ];
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id',$request->user()->id);
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function title()
    {
        return $this->title." - ".$this->category;
    }

    public function subtitle()
    {
        return "Author :".$this->user->name;
    }

    public static $globallySearchable=false;


    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make("title")->rules([
                'required'
            ]),
            Trix::make("body")->rules('required'),
            DateTime::make('Publish Post At','published_at')->hideFromIndex()->rules('after_or_equal:today'), // published_at
            DateTime::make('Publish Until At','published_until')->hideFromIndex()->rules('after_or_equal:published_at'),
            Boolean::make('Is Published?','is_published')
                ->canSee(function($request){
                    return true;
                }),
            Select::make("category")->options([
                'tutorial'=>"Tutorials",
                'news'=>'News'
            ])->hideWhenUpdating()->rules('required'),

            BelongsTo::make('User')->rules('required'),
            BelongsToMany::make('Tags'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new PostCount)->width('full'),
            (new PostPerday)->width('1/2'),
            (new PostPerCategory)->width('1/2')
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new PostPublished,
            // new PostCategory
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new MostTags
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new PublishedPost)->canSee(function($request){
                return true;
            })->canRun(function($request,$post){
                return $post->id===3;
            }),
            new UnPublishedPost,
        ];
    }
}
