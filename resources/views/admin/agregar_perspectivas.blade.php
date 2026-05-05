@extends('plantilla')
@section('title', 'Perspectivas')
@section('contenido')
@php
    use App\Models\Indicador;
    use App\Models\IndicadorLleno;
    use App\Models\Encuesta;
    use App\Models\Norma;
@endphp
    
<div class="container-fluid sticky-top">
    <div class="row bg-primary  d-flex align-items-center">
        <div class="col-12 col-sm-12 col-md-6 col-lg-10  pt-2 text-white">
            <h3 class="mt-1 mb-0 league-spartan">Perspectivas</h3>
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
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-wrap align-items-end gap-3">
                
                <form action="#" method="GET" class="d-flex flex-wrap align-items-end gap-3">
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

                <div class="ms-auto">
                    <a class="btn btn-primary" 
                       data-mdb-ripple-init 
                       data-mdb-modal-init 
                       data-mdb-target="#agregar_perspectiva">
                        <i class="fa-solid fa-plus-circle me-2"></i>
                        Agregar Perspectivas
                    </a>                   
                </div>
            </div>

            <div class="text-muted mb-0 mt-3">
                @php
                    $ponderacion_total = collect($perspectivas)->sum('ponderacion');
                @endphp
                <div class="badge {{ $ponderacion_total != 100 ? 'badge-danger' : 'badge-success' }}">
                    <i class="fa fa-exclamation-circle me-1"></i>
                    La suma de las ponderaciones de las Perspectivas es: <b>{{ $ponderacion_total }}%</b>
                </div>
            </div>
        </div>
    </div>
</div>





</div>




<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-10 col-md-9 col-lg-6 mb-4">
            <div class="card bg-success p-4 text-white text-center" id="card_cumplimiento">
                <h3>
                   <i class="fa " id="icono_card_cumplimiento"></i> Cumplimiento
                </h3>
                
                <h2 id="suma_cumplimientos"></h2>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            
                <!-- Departamentos Grid -->
            <div class="row justify-content-center">

                @php
                    $total_cumplimiento_perspectiva = 0;
                @endphp

                @forelse ($perspectivas as $perspectiva)
                    <div class="col-12 col-sm-12 col-md-10 col-lg-6 col-xl-6">
                        <div class="card border-0 w-100 shadow-sm h-100 department-card">
                            <div class="card-body  d-flex flex-column row">

                                <!-- Nombre del Departamento -->
                                <div class="flex-grow-1 mb-3 text-start col-12">
                                    <a href="{{ route('detalle.perspectiva', $perspectiva->id) }}" 
                                        class="text-decoration-none text-dark my-2">
                                        <h2 class="fw-bold mb-0 department-name text-center">
                                            {{ $perspectiva->nombre }}   
                                        </h2>
                                    </a>
                                </div>

                                    @foreach ($perspectiva->objetivos as $key_perspectiva => $objetivo)

                                    @php
                                        $indicadores_objetivo = Indicador::where('id_objetivo_perspectiva', $objetivo->id)->get();
                                        $encuesta_objetivo = Encuesta::where('id_objetivo_perspectiva', $objetivo->id)->get();
                                        $norma_objetivo = Norma::where('id_objetivo_perspectiva', $objetivo->id)->get();
                                    @endphp

        
                                    @foreach ($indicadores_objetivo as $indicador)

                                        @php
                                            $array_suma_cumplimientos = [];
                                            $indicadores_llenos = IndicadorLleno::where('id_indicador', $indicador->id)->where('final', 'on')->whereBetween('fecha_periodo', [$inicio, $fin])->get();


                                            foreach($indicadores_llenos as $indicador_lleno){
                                                array_push($array_suma_cumplimientos, $indicador_lleno->informacion_campo);
                                            }

                                        @endphp
                                        {{-- <h3>{{$indicador->nombre}}</h3> --}}



                           

                                    @if (!empty($array_suma_cumplimientos))

ww
                                            @php
                                                $promedio_cumplimiento;    
                                         
                                                    if ($indicador->tipo_indicador == "normal") {

                                                        // Caso normal
                                                        if ($indicador->unidad_medida == "porcentaje") {

                                                            $promedio_cumplimiento = round(
                                                                array_sum($array_suma_cumplimientos) / count($array_suma_cumplimientos),
                                                                2
                                                            );

                                                        } else {

                                                            $promedio_cumplimiento = round(
                                                                (array_sum($array_suma_cumplimientos) / (count($array_suma_cumplimientos) / $indicador->meta_esperada)) * 100
                                                            );

                                                        }

                                                    } else {

                                                        // Caso inverso
                                                        if ($indicador->unidad_medida == "porcentaje") {

                                                            $promedio_cumplimiento = 100 - round(
                                                                array_sum($array_suma_cumplimientos) / count($array_suma_cumplimientos),
                                                                2
                                                            );

                                                        } else {

                                                            $promedio_cumplimiento = round(
                                                                $indicador->meta_esperada / (array_sum($array_suma_cumplimientos) / count($array_suma_cumplimientos)) * 100,
                                                                2
                                                            );

                                                        }
                                                    }

                                                @endphp

                                    
                                    @else


                                    
                                    @endif

                                        

                                                {{-- <h1>{{array_sum($array_suma_cumplimientos) / count($array_suma_cumplimientos)}}</h1> --}}

                                            @endforeach

                                    @endforeach


                                @if (isset($promedio_cumplimiento))
                                    


                                <div class="col-12 text-center mb-3">
                                    <div class="row justify-content-center d-flex align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-center">Cumplimiento del objetivo: </h5>
                                            <h3 class="text-center fw-bold ">{{ $promedio_cumplimiento }}%</h3>
                                        </div>
                                        <div class="col-6 bg-light rounded-5">
                                            <h5 class="text-center">% Aportado a la perspectiva: </h5>
                                            <h3 class="text-center fw-bold cumplimiento_objetivo">
                                                
                                                {{ round( (  $promedio_cumplimiento / 100) * $perspectiva->ponderacion, 2)  }}%
                                            </h3>         
                         
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón Principal -->
                                 <div class=" flex-grow text-center mb-2 col-12">
                                    <a href="{{ route('detalle.perspectiva', $perspectiva->id) }}"
                                        class=" btn btn-light w-100 fw-semibold">
                                        <i class="fa-solid fa-magnifying-glass me-2"></i>                                        
                                        Explorar Perspectiva
                                    </a>
                                </div> 



                                <!-- Acciones -->
                                <div class="d-flex justify-content-end align-items-center pt-2 border-top row ">
                                    <div class="col-6 text-start fw-bold text-muted">
                                        <small>Ponderación: {{$perspectiva->ponderacion}}%</small>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" data-mdb-tooltip-init title="Editar " data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#edit_pe{{$perspectiva->id}}">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
    
                                            <button class="btn btn-sm btn-outline-danger" data-mdb-tooltip-init title="Eliminar" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#del_per{{$perspectiva->id}}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
            <!-- Empty State -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fa-solid fa-eye text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h5 class="text-muted mb-2">No se han registrado perspectivas.</h5>
                        <p class="text-muted mb-4">
                            <small>Comienza agregando tu primer perspectiva.</small>
                        </p>
                        <button class="btn btn-primary" data-mdb-ripple-init data-mdb-modal-init data-mdb-target="#agregar_perspectiva">
                            <i class="fa-solid fa-plus-circle me-2"></i>
                            Agregar Perspectiva
                        </button>
                    </div>
                </div>  
                @endforelse

            </div>

        </div>
    </div>
</div>





<div class="modal fade" id="agregar_perspectiva" tabindex="-1" aria-labelledby="agregarDepartamentoLabel" aria-hidden="true" data-mdb-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="agregarDepartamentoLabel">
                    <i class="fa-solid fa-plus-circle me-2"></i>
                    Agregar Perspectiva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form action="{{ route('perspectiva.store') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="text" class="form-control {{ $errors->first('nombre_perspectiva') ? 'is-invalid' : '' }}" id="nombre_perspectiva" name="nombre_perspectiva" |value="{{old('nombre_perspectiva')}}"required>
                                <label class="form-label" for="nombre_perspectiva">
                                    Perspectiva
                                    <span class="text-danger">*</span>
                                </label>
                                @if ($errors->first('nombre_perspectiva'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('nombre_perspectiva') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="number" min="1" max="100" class="form-control {{ $errors->first('ponderacion') ? 'is-invalid' : '' }}" id="ponderacion" name="ponderacion" |value="{{old('ponderacion')}}"required>
                                <label class="form-label" for="ponderacion">
                                    Ponderación
                                    <span class="text-danger">*</span>
                                </label>
                                @if ($errors->first('ponderacion'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('ponderacion') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-4">
                        <button type="button" class="btn btn-outline-secondary" data-mdb-ripple-init data-mdb-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" data-mdb-ripple-init>
                            <i class="fa-solid fa-save me-2"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- aqui va a ir el ciclo de la edicion y eliminacion de las perspectivas --}}
@foreach ($perspectivas as $perspectiva)
    <div class="modal fade" id="del_per{{$perspectiva->id}}" tabindex="-1" aria-labelledby="eliminarDepartamentoLabel{{$perspectiva->id}}" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="eliminarDepartamentoLabel{{$perspectiva->id}}">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-trash text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-semibold">¿Estás seguro de eliminar esta perspectiva?</h6>
                        <p class="text-muted mb-0">
                            <strong>{{$perspectiva->nombre}}</strong>
                        </p>

                        <small class="text-muted d-block">
                            Esta acción no se puede deshacer.
                        </small>
                    </div>
                    <form action="{{route('eliminar.perspectiva', $perspectiva->id)}}" method="POST">
                        @csrf 
                        @method('DELETE')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-fill" data-mdb-ripple-init data-mdb-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger flex-fill" data-mdb-ripple-init>
                                <i class="fa-solid fa-trash me-2"></i>
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    




    <div class="modal fade" id="edit_pe{{$perspectiva->id}}" tabindex="-1" aria-labelledby="editarDepartamentoLabel{{$perspectiva->id}}" aria-hidden="true" data-mdb-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="editarDepartamentoLabel{{$perspectiva->id}}">
                        <i class="fa-solid fa-edit me-2"></i>
                        Editar Perspectiva
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <form action="{{route('edit.perspectiva', $perspectiva->id)}}" method="POST">
                        @csrf 
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-outline" data-mdb-input-init>
                                    <input type="text"  class="form-control {{ $errors->first('nombre_departamento') ? 'is-invalid' : '' }}"  id="nombre_dep{{$perspectiva->id}}"  name="nombre_perspectiva"  value="{{old('nombre_perspectiva', $perspectiva->nombre)}}" required>
                                    <label class="form-label" for="nombre_pers{{$perspectiva->id}}">
                                        Nombre Perspectiva
                                        <span class="text-danger">*</span>
                                    </label>
                                    @if ($errors->first('nombre_perspectiva'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre_perspectiva') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-outline" data-mdb-input-init>
                                    <input type="number" min="1" max="100"  class="form-control {{ $errors->first('ponderacion_perspectiva') ? 'is-invalid' : '' }}"  id="ponderacion{{$perspectiva->id}}"  name="ponderacion_perspectiva"  value="{{old('ponderacion_perspectiva', $perspectiva->ponderacion)}}" required>
                                    <label class="form-label" for="ponderacion{{$perspectiva->id}}">
                                        Ponderación de la perspectiva
                                        <span class="text-danger">*</span>
                                    </label>
                                    @if ($errors->first('ponderacion_perspectiva'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('ponderacion_perspectiva') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-4">
                            <button type="button" class="btn btn-outline-secondary" data-mdb-ripple-init data-mdb-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" data-mdb-ripple-init>
                                <i class="fa-solid fa-save me-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>









@endforeach

















@endsection


@section('scripts')
    
<script>
const elementos = document.querySelectorAll('.cumplimiento_objetivo');

let suma = 0;

elementos.forEach(el => {
    // Obtener texto (ej: "85.50%")
    let texto = el.textContent;

    // Limpiar % y comas
    texto = texto.replace('%', '').replace(',', '');

    // Convertir a número
    let numero = parseFloat(texto);

    if (!isNaN(numero)) {
        suma += numero;
    }
});

// Mostrar resultado
document.getElementById('suma_cumplimientos').textContent = suma.toFixed(2) + '%';

let card_cumplimiento = document.getElementById('card_cumplimiento');
let icono_card_cumplimiento = document.getElementById('icono_card_cumplimiento');

if(suma < 80){
    card_cumplimiento.classList.add('bg-danger');
    icono_card_cumplimiento.classList.add("fa-xmark-circle");

}
else{
    card_cumplimiento.classList.add('bg-succes');
    icono_card_cumplimiento.classList.add("fa-check-circle");

}


</script>

@endsection