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
                                    <h4 class="card-text fw-bold">{{$indicador->nombre}}</h4>
                                    
                                        <span class="card-title fw-bold display-6 mt-3 h3">

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
                                <div class="col-auto mx-2">
                                    <a href="#" class="text-white" data-mdb-ripple-init data-mdb-tooltip-init data-mdb-placement="top" title="Ver historial" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#detall{{ $indicador->id }}">
                                        <i class="fa-solid fa-list"></i>
                                    </a>
                                </div>
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
                                    <h4 class="card-text fw-bold">{{$indicador->nombre}}</h4>
                                        <span class="card-title fw-bold display-6 mt-3 h3">

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
                                <div class="col-auto mx-2">
                                    <a href="#" class="text-white" data-mdb-ripple-init data-mdb-tooltip-init data-mdb-placement="top" title="Ver historial" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#detall{{ $indicador->id }}">
                                        <i class="fa-solid fa-list"></i>
                                    </a>
                                </div>
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
                                    <h4 class="card-text fw-bold">{{$indicador->nombre}}                               </h4>
                                        <span class="card-title fw-bold display-6 mt-3 h3">

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
                                <div class="col-auto mx-2">
                                    <a href="#" class="text-white" data-mdb-ripple-init data-mdb-tooltip-init data-mdb-placement="top" title="Ver historial" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#detall{{ $indicador->id }}">
                                        <i class="fa-solid fa-list"></i>
                                    </a>
                                </div>
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
            <div class="card text-white {{($norma->meta_minima > $norma->porcentaje_cumplimiento) ? 'bg-danger' : 'bg-success'}} shadow-2-strong">
                <a href="{{route('apartado.norma', $norma->id)}}" class="text-white w-100">
                <div class="card-body">
                    <div class="row justify-content-around d-flex align-items-center">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-7 ">
                            <h2 class="card-title fw-bold display-6 x">{{round($norma->porcentaje_cumplimiento, 2)}}%</h2>
                            <p class="card-text fw-bold">{{$norma->nombre}}</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-0 m-0">
                            <i class="fas fa-chart-line fa-3x"></i>
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


{{-- Foreach de las encuestas --}}



{{-- Foreach del cumplimiento a normas --}}
    @forelse ($encuestas as $encuesta)

        <div class="col-10 col-sm-10 col-md-6 col-lg-4 my-3">
            <div class="card text-white {{($encuesta->porcentaje_cumplimiento < $encuesta->meta_minima) ? 'bg-danger' : 'bg-success'}} shadow-2-strong">
                <a href="{{route('encuesta.index', $encuesta->id)}}" class="text-white w-100">
                <div class="card-body">
                    <div class="row justify-content-around d-flex align-items-center">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-7 ">
                            <h2 class="card-title fw-bold display-6 x">{{$encuesta->porcentaje_cumplimiento}}%</h2>
                            <p class="card-text fw-bold">{{$encuesta->nombre}}</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-0 m-0">
                            <i class="fas fa-chart-line fa-3x"></i>
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







{{-- MODALES CON GRAFICAS --}}
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered ">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fa fa-chart-simple"></i>
          Cumplimiento General {{ $departamento->nombre }}
        </h5>
        <small>Tomando en cuenta las ponderaciones</small>
        <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">

        <!-- Tabs -->
        <ul class="nav nav-tabs nav-justified mb-4" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-mdb-tab-init href="#tab-barras" role="tab">
              <i class="fa-solid fa-chart-column me-2"></i>Barras
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-mdb-tab-init href="#tab-linea" role="tab">
              <i class="fa-solid fa-chart-line me-2"></i>Línea
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-mdb-tab-init href="#tab-pie" role="tab">
              <i class="fa-solid fa-chart-pie me-2"></i>Pie
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-mdb-tab-init href="#tab-donut" role="tab">
              <i class="fa-solid fa-chart-donut me-2"></i>Donut
            </a>
          </li>
        </ul>

        <!-- Tabs content -->
        <div class="tab-content">

          <div class="tab-pane fade show active" id="tab-barras">
            <canvas id="chartBar" height="120"></canvas>
          </div>

          <div class="tab-pane fade" id="tab-linea">
            <canvas id="chartLine" height="120"></canvas>
          </div>

          <div class="tab-pane fade" id="tab-pie" >
            <div class="p-5 text-center row justify-content-center" style="max-height: 700px">
                <canvas id="chartPie" height="120"></canvas>
            </div>
          
        </div>

          <div class="tab-pane fade" id="tab-donut">
            <div class="p-5 text-center row justify-content-center" style="max-height: 700px">
                <canvas id="chartDonut" height="120"></canvas>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-mdb-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
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







@endsection


@section('scripts')

<script>

    const cumplimientoData = @json($cumplimiento_general);

    const labels = cumplimientoData.map(item => {
        const [year, month] = item.mes.split('-');

        // month - 1 porque JS empieza en 0
        const fecha = new Date(Number(year), Number(month) - 2, 1);

        const formatted = new Intl.DateTimeFormat('es-MX', {
            month: 'long',
            year: 'numeric'
        }).format(fecha);

        return formatted.charAt(0).toUpperCase() + formatted.slice(1);
    });

    const dataValues = cumplimientoData.map(item => item.total);


</script>



<script>
document.addEventListener('DOMContentLoaded', () => {



  // BARRAS
  new Chart(document.getElementById('chartBar'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Cumplimiento %',
        data: dataValues,
        backgroundColor: '#0d6efd'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, max: 100 }
      }
    }
  });



  // LINEA
new Chart(document.getElementById('chartLine'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Tendencia',
      data: dataValues,
      borderColor: '#198754',
      fill: false,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        min: 0,
        max: 100,
        ticks: {
          stepSize: 10
        }
      }
    },
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});





  // PIE
  new Chart(document.getElementById('chartPie'), {
    type: 'pie',
    data: {
      labels,
      datasets: [{
        data: dataValues,
        backgroundColor: [
        '#0a58ca', // primary dark
        '#0d6efd', // primary
        '#3d8bfd', // primary light
        '#6ea8fe'  // primary softer
        ]


      }]
    },
    options:{
        responsive:true,
        maininAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
  });



  // DONUT
  new Chart(document.getElementById('chartDonut'), {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data: dataValues,
        backgroundColor: ['#6610f2', '#20c997', '#fd7e14', '#0dcaf0']
      }]
    },
    options:{
        responsive:true,
        maininAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
  });

});
</script>

@endsection