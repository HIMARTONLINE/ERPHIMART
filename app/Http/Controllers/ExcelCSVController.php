<?php
 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
 
 
class ExcelCSVController extends Controller
{ 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function exportExcelCSV(Request $request) 
    {
        $categoria = $request->categoria;
        $de_stock = $request->de_stock;
        $a_stock = $request->a_stock;
        $de_precio = $request->de_precio;
        $a_precio = $request->a_precio;
        $de_fecha = $request->de_fecha;
        $a_fecha = $request->a_fecha;

        return Excel::download(new ProductsExport($categoria, $de_stock, $a_stock, $de_precio, $a_precio, $de_fecha, $a_fecha), 'products.xlsx');
    }
    
}