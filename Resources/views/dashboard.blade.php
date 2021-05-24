@extends('oauth2::layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <a href="{{url('/admin')}}" class="btn btn-secondary btn-sm mb-3"><- Назад в админку</a>
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <div class="mb-0">
                                <button id="providers" class="btn btn-light btn-sm" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Конфигурация
                                </button>
                            </div>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body table-responsive">
                                <form action="{{ url('/plugins/esia/config') }}" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="esia_client_id">Мнемоника ЕСИА</label>
                                                <input id="esia_client_id" type="text" class="form-control" name="esia_client_id" required value="{{ $config['clientId'] }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="esia_cert_path">Путь к сертификату</label>
                                                <input id="esia_cert_path" type="text" class="form-control" name="esia_cert_path" required value="{{ $config['certPath'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="esia_private_key_path">Путь к приватному ключу</label>
                                                <input id="esia_private_key_path" type="text" class="form-control" name="esia_private_key_path" required value="{{ $config['privateKeyPath'] }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="esia_private_key_password">Парольная фраза для приватного ключа</label>
                                                <input id="esia_private_key_password" type="text" class="form-control" name="esia_private_key_password" required value="{{ $config['privateKeyPassword'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="roles">Роль по умолчанию для нового пользователя</label>
                                                <select class="form-control" id="roles" name="esia_default_role">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}" @if($config['default_role'] == $role->id) selected @endif>{{ $role->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-check">
                                                <input type="hidden" name="esia_test" value="false">
                                                <input type="checkbox" @if($config['test']) checked @endif class="form-check-input" id="esia_test"  name="esia_test" value="true">
                                                <label class="form-check-label" for="esia_test">Тестовая среда</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">Сохранить</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <div class="mb-0">
                                <button id="clients" class="btn btn-light btn-sm" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    Запросы
                                </button>
                            </div>

                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                            <div class="card-body table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Ссылка</th>
                                        <th scope="col">Методы</th>
                                        <th scope="col">Описание</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>{{ $request->url }}</td>
                                            <td>{{ $request->methods }}</td>
                                            <td>{{ $request->description }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


