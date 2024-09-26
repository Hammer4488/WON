<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;

use Illuminate\view\View;

use Illuminate\http\RedirectResponse;

use Illuminate\support\Facades\storage;





class ProductController extends Controller
{
    /**
     * 
     * 
     * @return void
     */
    Public function index() : view 
    {
        $products = Product::latest()->paginate(10);

        return view ('products.index', compact('products'));

    
    }
    /**
     * create
     * @return view
     */
    
    public function create():view
    {
        return view('products.create');
    }
    /**
     * store
     * 
     * 
     * 
     */
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'image'               =>'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'               =>'required|min:5',
            'description'         =>'required|min:10',
            'price'               =>'required|numeric',
            'stock'               =>'required|numeric'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('products/', $image->hashName(),'public');
        
        //create product
        product::create([
            'image'              => $image->hashName(),
            'title'              => $request->title,
            'description'        => $request->description,
            'price'              => $request->price,
            'stock'              => $request->stock,

        ]);

        //redirect to index
        return redirect()->route('products.index')->with(['success'=>'Data Berhasil Disimpan']);
    }
    public function edit(string $id): view
    {
        //get product by ID
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
    }

    public function show(string $id): View
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function update(request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'image'               =>'image|mimes:jpeg,jpg,png|max:2048',
            'title'               =>'required|min:5', 
            'description'         =>'required|min:10', 
            'price'               =>'required|numeric', 
            'stock'               =>'required|numeric'
        ]);

        //get product by ID
        $product = product::findOrFail($id);

        //check if image is uploaded
        if($request->hasFile('image')){

            //upload new image
            $image = $request->file('image');
            $image->storeAs('products/', $image->hasName());

            //delete old image
            storage::delete('products/'.$product->image);

            //update product with new image
            $product->update([
                'image'                   =>$image->hahsName(),
                'title'                   =>$request->title(),
                'description'             =>$request->description(),
                'price'                   =>$request->price(),
                'stock'                   =>$request->stock()
            ]);

        }else{

            //updae product without image
            $product->update([
                'title'                   => $request->title,
                'description'             => $request->description,
                'price'                   => $request->price,
                'stock'                   => $request->stock
            ]);
        }
    
        //redirect to index
        return redirect()->route('products.index')->with(['success'=>'Data Berhasil Diubah']);

        //render view with product
        return view('products.show', compact('product'));
    }

    public function destroy($id): redirectresponse
    {
        //get product by ID
        $product = product::findOrFail($id);

        //delete image
        storage::delete('products/'. $product->image);

        //delete product
        $product->delete();

        //redirect to index
        return redirect()->route('products.index')->with(['success'=>'Data Berhasil Dihapus!']);
    }

}
