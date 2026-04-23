@extends('plantilla')
@section('title', 'Perspectivas')
@section('contenido')
@php
    use App\Models\Indicador;
    use App\Models\Norma;
    use App\Models\Encuesta;
    use App\Models\IndicadorLleno;
@endphp
<style>
/* Cuando el checkbox está seleccionado */
.btn-check:checked + .custom-check {
    background-color: #0d6efd; /* primary */
    color: #fff;
    border-color: #0d6efd;
}

/* Hover opcional más bonito */
.custom-check:hover {
    background-color: #e7f1ff;
}

/* Opcional: transición suave */
.custom-check {
    transition: all 0.2s ease;
}
</style>


<div class="container-fluid sticky-top">
    <div class="row bg-primary  d-flex align-items-center">
        <div class="col-12 col-sm-12 col-md-6 col-lg-10  pt-2 text-white">
            <h1 class="mt-1 mb-0 league-spartan">Objetivos</h1>
            @if (session('success'))
                <div class="text-white fw-bold ">
                    <i class="fa fa-check-circle mx-2"></i>
                    {{session('success')}}
                </div>
            @endif
            @if (session('editado'))
                <div class="text-white fw-bold ">
                    <i class="fa fa-check-circle mx-2"></i>
                    {{session('editado')}}
                </div>
            @endif

            @if (session('eliminado'))
                <div class="text-white fw-bold ">
                    <i class="fa fa-check-circle mx-2"></i>
                    {{session('eliminado')}}
                </div>
            @endif
        
            @if (session('encuesta_eliminada'))
                <div class="text-danger fw-bold ">
                    <i class="fa fa-check-circle mx-2"></i>
                    {{session('encuesta_eliminada')}}
                </div>
            @endif
            @if ($errors->any())
                <div class="text-white fw-bold bad_notifications">
                    <i class="fa fa-xmark-circle mx-2"></i>
                    {{$errors->first()}}
                </div>
            @endif
        </div>

        <div class="col-12 cl-sm-12 col-md-6 col-lg-2 text-center ">
            <form action="{{route('cerrar.session')}}" method="POST">
                @csrf 
                <button  class="btn btn-primary text-danger text-white fw-bold">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    @include('admin.assets.nav')



    <div class="row">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body py-3 px-4 text-center">

                <form action="#" method="GET">
                    <div class="d-flex flex-wrap align-items-end gap-3">

                        <!-- Fecha inicio -->
                        <div>
                            <label class="form-label small text-muted fw-semibold mb-1">Desde</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fa-solid fa-calendar-days text-primary"></i>
                                </span>
                                <input type="date"
                                    name="fecha_inicio"
                                    value="{{ request('fecha_inicio') ?? now()->format('Y-m-d') }}"
                                    class="form-control border-0 bg-light datepicker"
                                    onchange="this.form.submit()">
                            </div>
                        </div>

                        <!-- Fecha fin -->
                        <div>
                            <label class="form-label small text-muted fw-semibold mb-1">Hasta</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fa-solid fa-calendar-days text-danger"></i>
                                </span>
                                <input type="date"
                                    name="fecha_fin"
                                    value="{{ request('fecha_fin') ?? now()->format('Y-m-d') }}"
                                    class="form-control border-0 bg-light datepicker"
                                    onchange="this.form.submit()">
                            </div>
                        </div>

                        
                </form>
            </div>
        </div>
    </div>

</div>




</div>






<div class="container-fluid">

<div class="row">

    @forelse ($indicadores as $indicador)
        <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
            <div class="card text-white bg-danger shadow-2-strong">
                <a href="{{route('indicador.lleno.show.admin', $indicador->id)}}" class="text-white w-100">

                <div class="card-body">
                    <div class="row justify-content-around d-flex align-items-center">
                        <div class="col-12 ">
                            <h4 class="card-text fw-bold nombre">{{$indicador->nombre}}   </h4>
                                <span class="card-title fw-bold display-6 mt-3  resultado">

                                    @foreach ($indicador->indicadorLleno as $indicador_lleno)

                                            @php
                                                $ultimoFinal = $indicador->IndicadorLleno
                                                    ->where('final', 'on')
                                                    ->sortByDesc('id')
                                                    ->first();
                                            @endphp

                                    @endforeach

                                    @if ($ultimoFinal)
                                        {{ $ultimoFinal->informacion_campo }}
                                    @endif


                                    @php
                                        $ultimo = $indicador->IndicadorLleno;
                                    @endphp


                                </span>                        

                        </div>

                    </div>

                    <div class="row justify-content-around my-2">
                        <div class="col-auto text-center">
                            <span>
                                <i class="fa fa-arrow-up"></i>
                                Meta: {{ $indicador->meta_esperada }}
                            </span>
                        </div>
                        <div class="col-auto text-center">
                            <span class="mx-5">
                                <i class="fa-solid fa-circle-down"></i>
                                Min.: {{ $indicador->meta_minima }}
                            </span>
                        </div>
                    </div>
                </div>
                </a>
                <div class="card-footer ">
                        <div class="row justify-content-between ">
                            <div class="col-auto h4">
                                @php
                                $tipos = [
                                    "g" => "<i class='fa-solid fa-city'></i> Indicador General",
                                    "p" => "<i class='fa-solid fa-cow'></i> Pecuarios",
                                    "m" => "<i class='fa-solid fa-dog'></i> Mascotas",
                                ];
                                @endphp

                                {!!  
                                    empty($indicador->planta)
                                        ? "<i class='fa-solid fa-circle-exclamation'></i> Sin asignación"
                                        : ($tipos[strtolower($indicador->planta)] 
                                            ?? " <i class='fa-solid fa-industry'></i> Planta {$indicador->planta}")
                                !!}
                            </div>
                            {{-- <div class="col-auto mx-2">
                                <a href="#" class="text-white" data-mdb-ripple-init data-mdb-tooltip-init data-mdb-placement="top" title="Ver historial" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#detall{{ $indicador->id }}">
                                    <i class="fa-solid fa-list"></i>
                                </a>
                            </div> --}}
                        </div>
                </div>                        
            </div>
        </div>
    @empty
        
    @endforelse

</div>


</div>



@endsection