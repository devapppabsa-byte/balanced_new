@extends('plantilla')
@section('title', 'Lista de Indicadores del departamento')
@section('contenido')
@php
    use Carbon\Carbon;
    use App\Models\MetaIndicador;
    use App\Models\IndicadorLleno;
@endphp
<div class="container-fluid">
    <div class="row bg-primary d-flex align-items-center">
        <div class="col-12 col-sm-12 col-md-6 col-lg-10  pt-2 text-white">
            <h1 class="mt-1 mb-0">
                {{$departamento->nombre}}
            </h1>

            <h3>
                Lista de indicadores de {{$departamento->nombre}}
            </h3>

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
 
</div>
{{-- 
Aqui yacen los restosa de algo que pudo ser y no fue (si puede ser solo que todos los indicadores terminen en porcentaje)
<div class="container-fluid">
    <div class="row  border-bottom  bg-white border-bottom shadow-sm">


        <div class="col-12 col-sm-12 col-md-4 col-lg-3 my-1">
            <button  class="btn btn-sm btn-primary w-100"  data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#exampleModal">
                <i class="fa fa-eye mx-1"></i>
                Cumplimiento general
            </button>
        </div>


    </div>
</div> --}}




<div class="container-fluid">
    <div class="row jusfify-content-center">
        @forelse ($indicadores as $indicador)
                @php
                    $contador = 0;
                    $suma = 0;

                    $resultado = [];
                @endphp
            @foreach($indicador->indicadorLleno as $indicador_lleno)

                @if ($indicador_lleno->final == 'on')

                    @php
                        $contador++;
                        $suma = $suma + $indicador_lleno->informacion_campo;
                        array_push($resultado,$indicador_lleno->informacion_campo ) 
                    @endphp


                @endif

                
                @endforeach
                


            @if ($contador > 0)
            @php
                $cumplimiento = end($resultado);
            @endphp

                @if ($indicador->variacion === 'on')
                    
                <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">

                    <div class="card text-white {{($cumplimiento >= ($indicador->meta_esperada - $indicador->meta_minima)&& $cumplimiento <= ($indicador->meta_esperada + $indicador->meta_minima)) ? 'bg-success' : 'bg-danger'}} shadow-2-strong">
                    
                    
                        <a href="{{route('indicador.lleno.show.admin', $indicador->id)}}" class="text-white w-100">
                        <div class="card-body">
                            <div class="row justify-content-around d-flex align-items-center">
                                <div class="col-12 ">
                                    <h4 class="card-text fw-bold nombre">{{$indicador->nombre}}</h4>
                                    
                                        <span class="card-title fw-bold display-6 mt-3 h3 resultado">

                                            @if($indicador->unidad_medida === 'pesos')
                                                ${{ number_format($cumplimiento, 2) }}

                                            @elseif($indicador->unidad_medida === 'porcentaje')
                                                {{ round($cumplimiento, 2) }}%

                                            @elseif($indicador->unidad_medida === 'dias')
                                                {{ round($cumplimiento, 2) }} Días

                                            @elseif($indicador->unidad_medida === 'toneladas')
                                                {{ round($cumplimiento, 2) }} Ton.

                                            @else
                                                {{ round($cumplimiento, 2) }}
                                            @endif

                                        </span>

                                        <input type="hidden" class="meta_minima" value="{{ $indicador->meta_minima }}">
                                        <input type="hidden" class="meta_maxima" value="{{ $indicador->meta_esperada }}" >
                                        <input type="hidden" class="resultado_obtenido" value="{{ $cumplimiento }}">
                                        <input type="hidden" class="tipo_indicado" value="variacion"> 
                                                                            
                                </div>

                                <div class="col-12 my-2">
                                    <div class="row justify-content-around">
                                        <div class="col-auto">
                                            <span>
                                                <i class="fa fa-arrow-up"></i>
                                                Meta: {{ $indicador->meta_esperada }}
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa-solid fa-up-down"></i>
                                            <span>Variacion: {{ $indicador->meta_minima }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </a>
                        <div class="card-footer p-2">
                            <div class="row  d-flex justify-content-between align-items-center">
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
                

                @else




                @if ($indicador->tipo_indicador === "riesgo")

                <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
                    <div class="card text-white {{($cumplimiento < $indicador->meta_minima) ? 'bg-success' : 'bg-danger'}} shadow-2-strong">
                        <a href="{{route('indicador.lleno.show.admin', $indicador->id)}}" class="text-white w-100">

                        <div class="card-body">
                            <div class="row justify-content-around d-flex align-items-center">
                                <div class="col-12 ">
                                    <h4 class="card-text fw-bold nombre">{{$indicador->nombre}}</h4>
                                        <span class="card-title fw-bold display-6 mt-3 h3 resultado">

                                            @if($indicador->unidad_medida === 'pesos')
                                                ${{ number_format($cumplimiento, 2) }}

                                            @elseif($indicador->unidad_medida === 'porcentaje')
                                                {{ round($cumplimiento, 2) }}%

                                            @elseif($indicador->unidad_medida === 'dias')
                                                {{ round($cumplimiento, 2) }} Días

                                            @elseif($indicador->unidad_medida === 'toneladas')
                                                {{ round($cumplimiento, 2) }} Ton.

                                            @else
                                                {{ round($cumplimiento, 2) }}
                                            @endif
                                        </span>

                                        <input type="hidden" class="meta_minima" value="{{ $indicador->meta_minima }}">
                                        <input type="hidden" class="resultado_obtenido" value="{{ $cumplimiento }}">
                                        <input type="hidden" class="tipo_indicado" value="riesgo">                                


                                </div>

                            </div>

                            <div class="row justify-content-around my-2">
                                <div class="col-auto text-center">
                                    <span>
                                        <i class="fa fa-arrow-up"></i>
                                        Metas: {{ $indicador->meta_esperada }}
                                    </span>
                                </div>
                                <div class="col-auto text-center">
                                    <span class="mx-5">
                                        <i class="fa-solid fa-circle-down"></i>
                                        Limite: {{ $indicador->meta_minima }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </a>
                        <div class="card-footer p-2">
                                <div class="row  d-flex justify-content-between align-items-center">
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
                


                @else
                <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
                    <div class="card text-white {{($cumplimiento <= $indicador->meta_minima) ? 'bg-danger' : 'bg-success'}} shadow-2-strong">
                        <a href="{{route('indicador.lleno.show.admin', $indicador->id)}}" class="text-white w-100">

                        <div class="card-body">
                            <div class="row justify-content-around d-flex align-items-center">
                                <div class="col-12 ">
                                    <h4 class="card-text fw-bold nombre">{{$indicador->nombre}}                               </h4>
                                        <span class="card-title fw-bold display-6 mt-3 h3 resultado">

                                            @if($indicador->unidad_medida === 'pesos')
                                                ${{ number_format($cumplimiento, 2) }}

                                            @elseif($indicador->unidad_medida === 'porcentaje')
                                                {{ round($cumplimiento, 2) }}%

                                            @elseif($indicador->unidad_medida === 'dias')
                                                {{ round($cumplimiento, 2) }} Días

                                            @elseif($indicador->unidad_medida === 'toneladas')
                                                {{ round($cumplimiento, 2) }} Ton.

                                            @else
                                                {{ round($cumplimiento, 2) }}
                                            @endif

                                        </span>

                                <input type="hidden" class="meta_minima" value="{{ $indicador->meta_minima }}">
                                <input type="hidden" class="resultado_obtenido" value="{{ $cumplimiento }}">
                                <input type="hidden" class="tipo_indicado" value="normal">                                

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
                    
                @endif


                    
                @endif






            @else

                <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
                    <div class="card text-white bg-dark shadow-2-strong">
                        <a href="{{route('indicador.lleno.show.admin', $indicador->id)}}" class="text-white w-100">
                        <div class="card-body">
                            <div class="row justify-content-around d-flex align-items-center">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-7 ">
                                    <h5 class="card-title fw-bold  x">
                                        Sin registros aún.
                                    </h5>
                                    <p class="card-text fw-bold">{{$indicador->nombre}}</p>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-0 m-0">
                                    <i class="fas fa-chart-line fa-3x"></i>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
            @endif


        @empty

        @endforelse








{{-- Foreach de las encuestas --}}


    @forelse ($normas as $norma)
        <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
            <div class="card text-white {{($norma->meta_minima > $norma->porcentaje_mes) ? 'bg-danger' : 'bg-success'}} shadow-2-strong">
                <a href="{{route('apartado.norma', $norma->id)}}" class="text-white w-100">
                <div class="card-body">
                    <div class="row justify-content-around d-flex align-items-center">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-7 ">
                            <h2 class="card-title fw-bold display-6 resultado">{{round($norma->porcentaje_mes, 2)}}%</h2>
                            <p class="card-text fw-bold nombre">{{$norma->nombre}}</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-0 m-0">
                            <i class="fas fa-chart-line fa-3x"></i>
                            <input type="hidden" class="meta_minima" value="{{ $norma->meta_minima }}">
                            <input type="hidden" class="resultado_obtenido" value="{{ $norma->porcentaje_mes }}">
                            <input type="hidden" class="tipo_indicado" value="normal">

                        </div>
                    </div>
                </div>
                <div class="card-footer p-2">
                    <div class="row  d-flex justify-content-between align-items-center">
                        <div class="col-12">
                           <h4 class="">
                             {{ $norma->planta }}
                           </h4>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>
    @empty

    @endforelse 


{{-- Foreach de las encuestas --}}



{{-- Foreach del cumplimiento a normas --}}
    @forelse ($encuestas as $encuesta)

        <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
            <div class="card text-white {{($encuesta->porcentaje_mes < $encuesta->meta_minima) ? 'bg-danger' : 'bg-success'}} shadow-2-strong">
                <a href="{{route('encuesta.index', $encuesta->id)}}" class="text-white w-100">
                <div class="card-body">
                    <div class="row justify-content-around d-flex align-items-center">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-7 ">
                            <h2 class="card-title fw-bold display-6 x resultado">{{$encuesta->porcentaje_mes}}%</h2>
                            <p class="card-text fw-bold nombre">{{$encuesta->nombre}}</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-0 m-0">
                            <i class="fas fa-chart-line fa-3x"></i>
                            <input type="hidden" class="meta_minima" value="{{ $encuesta->meta_minima }}">
                            <input type="hidden" class="resultado_obtenido" value="{{ $encuesta->porcentaje_mes }}">
                            <input type="hidden" class="tipo_indicado" value="normal">
                        </div>
                    </div>
                </div>
                <div class="card-footer p-2">
                        <div class="row  d-flex justify-content-between align-items-center">
                            <div class="col-auto">
                                    Ver Detalles
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-circle-right"></i>
                            </div>
                        </div>
                </div>
                </a>
            </div>
        </div>

    @empty

    @endforelse

{{-- Foreach del cumplimiento a normas --}}
    </div>


    @if ($encuestas->isEmpty()  &&  $indicadores->isEmpty() )
        <div class="row mt-5 justify-content-center">
            <div class="col-9 p-5 text-center bg-white shadow shadow-sm border">
                <h4>
                    <i class="fa fa-exclamation-circle text-danger"></i>
                    No se encontro Información.
                </h4>
            </div>
        </div>
    @endif




</div>












{{-- Modales del detalle historico de los indocadores --}}
@foreach ($indicadores as $indicador)

<div class="modal fade" id="detall{{ $indicador->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header bg-primary py-4">
            <h4 class="text-white" id="exampleModalLabel">
                <i class="fa fa-calendar mx-1"></i>
                Historial de {{ $indicador->nombre }} -

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
                                    ?? "<i class='fa-solid fa-industry'></i> Planta {$indicador->planta}")
                        !!}

                        @php
                            $promedios = IndicadorLleno::select(
                                DB::raw('YEAR(fecha_periodo) as anio'),
                                DB::raw('AVG(informacion_campo) as promedio')
                            )
                            ->where('final', 'on')
                            ->where('id_indicador', $indicador->id)
                            ->groupBy(DB::raw('YEAR(fecha_periodo)'))
                            ->orderBy('anio')
                            ->get();
                        @endphp  



            </h4>
            <button type="button" class="btn-close " data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
        </div>
            <div class="modal-body ">
  
                
                <div class="row justify-content-center mb-2">
                    @forelse ($promedios as $promedio)
                        <div class="col-auto p-3 border border-3 border-dark rounded m-2 text-center">
                            <h5>Promedio Anual</h5>
                            <div class="row">
                                <div class="col-12">
                                        <span class="card-title fw-bold  mt-3 h3">

                                            @if($indicador->unidad_medida === 'pesos')
                                                ${{ number_format($promedio->promedio, 2) }}

                                            @elseif($indicador->unidad_medida === 'porcentaje')
                                                {{ round($promedio->promedio, 2) }}%

                                            @elseif($indicador->unidad_medida === 'dias')
                                                {{ round($promedio->promedio, 2) }} Días

                                            @elseif($indicador->unidad_medida === 'toneladas')
                                                {{ round($promedio->promedio, 2) }} Ton.

                                            @else
                                                {{ round($promedio->promedio, 2) }}
                                            @endif

                                        </span>
                                </div>
                                <div class="col-12">
                                    <span class="fw-bold">Año: {{ $promedio->anio }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        
                    @endforelse
                </div>

                <div class="row justify-content-center px-4">
                    <div class="list-group shadow-sm rounded-4">
                        {{-- aqui dentro estan las tarjetitas que muestran el istorial de los meses llenados. --}}
                        @forelse ($indicador->indicadorLleno->reverse() as $indicador_lleno)

                            @if ($indicador_lleno->final === 'on')

               
                                    @php
                                    
                                        $resultado = $indicador_lleno->informacion_campo;
                                        $metas_indicador = MetaIndicador::where('id_movimiento_indicador_lleno', $indicador_lleno->id_movimiento)->first();
                                        
                                        $semaforizacion = "";
                                        $icono = "";
                                        $texto_meta_minimo = "";
                                        $texto_meta_maxima = "";
                                        $meta_maxima = $metas_indicador->meta_maxima;
                                        $meta_minima = $metas_indicador->meta_minima;
                                        $id_movimiento = $indicador_lleno->id_movimiento;
                                        
                                        
                                        if($indicador->tipo_indicador === "riesgo"){
                                            $texto_meta_minimo = "Aceptable";


                                            if($resultado <= $meta_minima){
                                           
                                                $semaforizacion = 'text-success';
                                                $icono = '<i class="fa-solid fa-circle-check text-success"></i>';
                                           
                                            }
                                            else{ 

                                                $semaforizacion = 'text-danger';
                                                $icono = '<i class="fa-solid text-danger fa-triangle-exclamation"></i>';
                                            
                                             }
                                        
                                        }

                                        
                                        if($indicador->tipo_indicador === "normal"){
                                            $texto_meta_minimo = "Meta Minima";

                                            if($resultado < $meta_minima){
                                                $semaforizacion = 'text-danger';
                                                $icono = '<i class="fa-solid text-danger fa-triangle-exclamation"></i>';

                                            }
                                            else{        
                                                $semaforizacion = 'text-success';
                                                $icono = '<i class="fa-solid fa-circle-check text-success"></i>';

                                            }                                        
                                        }



                                        if($indicador->variacion === "on"){
                                            $variacion = $meta_minima; // aquí se usa como margen
                                            $limite_inferior = $meta_maxima - $variacion;
                                            $limite_superior = $meta_maxima + $variacion;
                                            $texto_meta_minimo = "Variación";

                                            if($resultado >= $limite_inferior && $resultado <= $limite_superior){
                                                $semaforizacion = 'text-success'; // dentro del rango
                                                $icono = '<i class="fa-solid fa-circle-check text-success"></i>';

                                            } else {
                                                $semaforizacion = 'text-danger'; // fuera del rango
                                                $icono = '<i class="fa-solid text-danger fa-triangle-exclamation"></i>';
                                                
                                            }
                                        }
                                    @endphp


                                    <a href="#" class="list-group-item list-group-item-action p-3 ">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold">{{ $indicador_lleno->nombre_campo }}</h6>


                                            <span class="card-title {{ $semaforizacion }} fw-bold">
                                                {!! $icono !!}
                                                @if($indicador->unidad_medida === 'pesos')
                                                    $ {{ $indicador_lleno->informacion_campo }}

                                                @elseif($indicador->unidad_medida === 'porcentaje')
                                                    {{ $indicador_lleno->informacion_campo }}%

                                                @elseif($indicador->unidad_medida === 'dias')
                                                    {{ $indicador_lleno->informacion_campo }} Días

                                                @elseif($indicador->unidad_medida === 'toneladas')
                                                    {{ $indicador_lleno->informacion_campo }} Ton.

                                                @else
                                                    {{ $indicador_lleno->informacion_campo }}
                                                @endif

                                            </span>


                                        </div>
                                        <p class="mb-1 h4">
                                            
                                            <i class="fa fa-calendar"></i>
                                            {{Carbon::parse($indicador_lleno->fecha_periodo)->translatedFormat('F Y')}}.
                                        
                                        </p>
                                        <p class="fw-bold">
                                            <i class="fa-solid fa-chart-line"></i>
                                            <span>
                                                Meta: {{ $meta_maxima }}
                                            </span>
                                                -
                                            <span>
                                                {{ $texto_meta_minimo }}: {{ $meta_minima }}
                                            </span>
                                        </p>
        
                              
                                    </a>

                            @endif
                        @empty
                            
                        @endforelse
    



                        {{-- aqui dentro estan las tarjetitas que muestran el istorial de los meses llenados. --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer text-start h4">
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
        </div>
    </div>
</div>

@endforeach



<button class="btn btn-dark flotante" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#grafico" style="z-index: 9999">
    <i class="fa-solid fa-chart-column"></i>
    Gráfica general
</button>

<div class="modal fade" id="grafico" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-mdb-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="exampleModalLabel">Gráfica</h5>
        <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
            <div class="row justify-content-center">
                <div class="col-10">
                    <!-- Tabs navs -->
                    <ul class="nav nav-tabs nav-justified mb-3" id="ex1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a
                        data-mdb-tab-init
                        class="nav-link active"
                        id="ex3-tab-1"
                        href="#ex3-tabs-1"
                        role="tab"
                        aria-controls="ex3-tabs-1"
                        aria-selected="true"
                        >Gráfica en Horizontal</a
                        >
                    </li>
                    <li class="nav-item" role="presentation">
                        <a
                        data-mdb-tab-init
                        class="nav-link"
                        id="ex3-tab-2"
                        href="#ex3-tabs-2"
                        role="tab"
                        aria-controls="ex3-tabs-2"
                        aria-selected="false"
                        >Gráfica en Vertical</a
                        >
                    </li>
                    </ul>
                    <!-- Tabs navs -->

                    <!-- Tabs content -->
                    <div class="tab-content" id="ex2-content">
                    <div
                        class="tab-pane fade show active"
                        id="ex3-tabs-1"
                        role="tabpanel"
                        aria-labelledby="ex3-tab-1"
                    >
                        <canvas id="horizontal"></canvas>
                    </div>
                    <div
                        class="tab-pane fade"
                        id="ex3-tabs-2"
                        role="tabpanel"
                        aria-labelledby="ex3-tab-2"
                    >

                        <canvas id="vertical"></canvas>
                    </div>

                    </div>
                    <!-- Tabs content -->
                </div>
            </div>
            <div id="mensajeVacio" class="text-center fw-bold my-3 text-danger" style="display:none;">
                 No se encontró información para graficar
            </div>
  
        </div>
    </div>
  </div>
</div>




@endsection


@section('scripts')



<script>
function obtenerDatos() {
    const nombres = document.querySelectorAll('.nombre');
    const resultados = document.querySelectorAll('.resultado_obtenido');
    const metasMin = document.querySelectorAll('.meta_minima');
    const metasMax = document.querySelectorAll('.meta_maxima');
    const tiposIndicador = document.querySelectorAll('.tipo_indicador');

    let labels = [];
    let data = [];
    let colores = [];
    let lineaMetaMin = [];
    let lineaMetaMax = [];

    const VERDE = 'rgba(0, 200, 83, 0.9)';
    const ROJO  = 'rgba(211, 47, 47, 0.9)';

    resultados.forEach((el, i) => {
        let valor = parseFloat(el.value);

        // 👉 si viene vacío lo tomamos como 0
        if (isNaN(valor)) valor = 0;

        let metaMin = parseFloat(metasMin[i]?.value ?? 0);
        let metaMax = parseFloat(metasMax[i]?.value ?? metaMin);
        let tipo = tiposIndicador[i]?.value ?? '';

        let nombre = nombres[i]?.innerText.trim() ?? 'N/A';

        labels.push(nombre);
        data.push(valor);
        lineaMetaMin.push(metaMin);
        lineaMetaMax.push(metaMax);

        let color = 'gray';

        if (tipo === 'variacion') {
            let li = metaMax - metaMin;
            let ls = metaMax + metaMin;

            color = (valor >= li && valor <= ls) ? VERDE : ROJO;
        } 
        else if (tipo === 'riesgo') {
            color = valor > metaMin ? ROJO : VERDE;
        } 
        else {
            color = valor > metaMin ? VERDE : ROJO;
        }

        colores.push(color);
    });

    return { labels, data, colores, lineaMetaMin, lineaMetaMax };
}

function crearGrafica() {



    const { labels, data, colores, lineaMetaMin, lineaMetaMax } = obtenerDatos();

    const mensaje = document.getElementById('mensajeVacio');
    const canvas = document.getElementById('vertical');

    // 🚨 VALIDACIÓN
    const todosCero = data.every(v => v === 0);

    if (data.length === 0 || todosCero) {
        mensaje.style.display = 'block';
        canvas.style.display = 'none';
        return;
    } else {
        mensaje.style.display = 'none';
        canvas.style.display = 'block';
    }

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Resultado',
                    data: data,
                    backgroundColor: colores
                },
                {
                    type: 'line',
                    label: 'Meta mínima',
                    data: lineaMetaMin,
                    borderColor: 'orange',
                    borderWidth: 2,
                    fill: false
                },
                {
                    type: 'line',
                    label: 'Meta máxima',
                    data: lineaMetaMax,
                    borderColor: 'blue',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            indexAxis: 'x'
        }
    });
}

crearGrafica();


</script>


<script>
function recolectarIndicadores() {
    const listaNombres = document.querySelectorAll('.nombre');
    const listaResultados = document.querySelectorAll('.resultado_obtenido');
    const listaMetaMinima = document.querySelectorAll('.meta_minima');
    const listaMetaMaxima = document.querySelectorAll('.meta_maxima');
    const listaTipoIndicador = document.querySelectorAll('.tipo_indicador');

    let etiquetas = [];
    let valores = [];
    let coloresBarras = [];
    let metasMinimas = [];
    let metasMaximas = [];

    const COLOR_OK = 'rgba(0, 200, 83, 0.9)';
    const COLOR_ERROR = 'rgba(211, 47, 47, 0.9)';

    listaResultados.forEach((inputResultado, index) => {
        let valorActual = parseFloat(inputResultado.value);

        if (isNaN(valorActual)) valorActual = 0;

        let metaMin = parseFloat(listaMetaMinima[index]?.value ?? 0);
        let metaMax = parseFloat(listaMetaMaxima[index]?.value ?? metaMin);
        let tipo = listaTipoIndicador[index]?.value ?? '';

        let nombreIndicador = listaNombres[index]?.innerText.trim() ?? 'N/A';

        etiquetas.push(nombreIndicador);
        valores.push(valorActual);
        metasMinimas.push(metaMin);
        metasMaximas.push(metaMax);

        let colorAsignado = 'gray';

        if (tipo === 'variacion') {
            let limiteInferior = metaMax - metaMin;
            let limiteSuperior = metaMax + metaMin;

            colorAsignado = (valorActual >= limiteInferior && valorActual <= limiteSuperior) 
                ? COLOR_OK 
                : COLOR_ERROR;
        } 
        else if (tipo === 'riesgo') {
            colorAsignado = valorActual > metaMin ? COLOR_ERROR : COLOR_OK;
        } 
        else {
            colorAsignado = valorActual > metaMin ? COLOR_OK : COLOR_ERROR;
        }

        coloresBarras.push(colorAsignado);
    });

    return { etiquetas, valores, coloresBarras, metasMinimas, metasMaximas };
}

function generarGraficaIndicadores() {

    const { etiquetas, valores, coloresBarras, metasMinimas, metasMaximas } = recolectarIndicadores();

    const mensajeVacio = document.getElementById('mensajeVacio');
    const canvasGrafica = document.getElementById('horizontal');

    const todosEnCero = valores.every(v => v === 0);

    if (valores.length === 0 || todosEnCero) {
        mensajeVacio.style.display = 'block';
        canvasGrafica.style.display = 'none';
        return;
    } else {
        mensajeVacio.style.display = 'none';
        canvasGrafica.style.display = 'block';
    }

    const contexto = canvasGrafica.getContext('2d');

    new Chart(contexto, {
        data: {
            labels: etiquetas,
            datasets: [
                {
                    type: 'bar',
                    label: 'Resultado',
                    data: valores,
                    backgroundColor: coloresBarras
                },
                {
                    type: 'line',
                    label: 'Meta mínima',
                    data: metasMinimas,
                    borderColor: 'orange',
                    borderWidth: 2,
                    fill: false
                },
                {
                    type: 'line',
                    label: 'Meta máxima',
                    data: metasMaximas,
                    borderColor: 'blue',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            indexAxis: 'y' // 👈 aquí ya la dejé horizontal de una vez
        }
    });
}

generarGraficaIndicadores();
</script>



@endsection