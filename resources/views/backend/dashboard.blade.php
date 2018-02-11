@extends('backend.layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong>PictoGr&#0252;v Generator</strong>
                </div><!--card-header-->
                <div class="card-block">
                    <div class="row">
                        {{ html()->form('POST', route('admin.dashboard'))->class('form-horizontal')->open() }}
                            <div class="form-group">
                                <label for="ig_username"><strong>Input Username</strong></label>
                                <input type="text" value="{{request()->ig_username}}" name="ig_username" class="form-control" placeholder="lordguirk" required/>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-default pull-left" type="submit">Search</button>
                            </div>
                        {{ html()->form()->close() }}
                    </div>
                </div><!--card-block-->
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
    
    <div class="row">

        @if(!is_null($instaErrors))
            <div class="col-md-12">        
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{$instaErrors}}</h5>
                        @if($instaErrors == 'User not found')
                            <p class="card-text">The username you chose does not exist! Please try another name.</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            @foreach($items as $item)
            <div class="col-sm-6 col-md-4 col-lg-3">        
                <div class="card">
                    @if (!is_null($item) && isset($item->url))
                        <img class="card-img-top" src="{{$item->url}}" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">{{$item->like_count}} likes</h5>
                            <p class="card-text">{{$item->caption->text}}</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Uploaded on {{date("m/d/Y",$item->taken_at)}}</small>
                        </div>
                    @elseif (!is_null($item) && !is_null($item->carousel_media))
                        @foreach ($item->carousel_media as $media)
                            <img class="card-img-top" src="{{$media->getImageVersions2()->candidates[0]->url}}" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">{{$item->like_count}} likes</h5>
                                <p class="card-text">{{$item->caption->text}}</p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">Uploaded on {{date("m/d/Y",$item->taken_at)}}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>
@endsection