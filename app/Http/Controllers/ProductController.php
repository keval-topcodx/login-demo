<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use function League\Uri\UriTemplate\replaceList;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('id')->paginate(10);
        return view('product.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $input = $request->validated();
//        $imagePath = [];
//
//        foreach ($request->file('images') as $image) {
//            $path = $image->store('uploads', 'public');
//            $imagePath[] = $path;
//        }
//        $jsonPath = $imagePath;

        $product = Product::create([
            'title' => $input['title'],
            'description' => $input['description'],
            'status' => $input['status'],
//            'images' => $jsonPath,
        ]);
        foreach ($request->file('images') as $image) {
            $product
                ->addMedia($image)
                ->toMediaCollection('products');
        }

        foreach ($input['variants'] as $variant) {
            $productVariant = $product->variants()->create([
               'title' => $variant['title'],
                'price' => $variant['price'],
                'sku' => $variant['sku'],
            ]);
        }

        $tags = json_decode($input['productTags']);

        $productTags = [];
        if ($tags) {
            foreach ($tags as $tag) {
                $existingTag = Tag::find(strtolower($tag));
                if($existingTag) {
                    $productTags[] = $existingTag;
                } else {
                    $newTag = Tag::create([
                        'name' => $tag,
                    ]);
                    $productTags[] = $newTag->id;
                }
            }
        }


        $product->tags()->sync($productTags);

        return redirect()->route('products.index')->with('success', 'Product Inserted Successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('product.edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $input = $request->validated();
//        $imagePath = [];

//        if($request->hasFile('images')) {
//            $oldImages = $product->images;
//            foreach ($oldImages as $oldImage) {
//                Storage:: disk('public')->delete($oldImage);
//            }
//            foreach ($request->file('images') as $image) {
//                $path = $image->store('uploads', 'public');
//                $imagePath[] = $path;
//            }
//        }
//        if(!$imagePath) {
//            $imagePath = $product->images;
//        }
        $product->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'status' => $input['status'],
//            'images' => $imagePath,
        ]);

        if ($request->hasFile('images')) {
            $product->clearMediaCollection('products');
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('products');
            }
        }

        $variants = $product->variants;
        foreach ($variants as $variant) {
            $variant->delete();
        }

        foreach ($input['variants'] as $variant) {
            $productVariant = $product->variants()->create([
                'title' => $variant['title'],
                'price' => $variant['price'],
                'sku' => $variant['sku'],
            ]);
        }

        $tags = json_decode($input['productTags']);

        $productTags = [];
        if ($tags) {
            foreach ($tags as $tag) {
                $existingTag = Tag::find(strtolower($tag));
                if($existingTag) {
                    $productTags[] = $existingTag;
                } else {
                    $newTag = Tag::create([
                        'name' => $tag,
                    ]);
                    $productTags[] = $newTag->id;
                }
            }
        }
        $product->tags()->sync($productTags);

        return redirect()->route('products.index')->with('success', 'Product Updated Successfully');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return Redirect::route('products.index')->with('success', 'Product deleted successfully!');
    }

     public function getTags(Request $request)
     {
         $data = $request->input();
         $search = $data['search'];
         $tags = Tag::select('id', 'name')->where('name', 'like', $search . '%')->get();

         if(count($tags) !== 0) {
             return response()->json([
                 'success' => true,
                 'tags' => $tags,
             ]);
         } else {
             return response()->json([
                'success' => false,
             ]);
         }
     }

     public function searchProducts(Request $request)
     {
         $data = $request->input("search");

         if (strlen(trim($data)) > 0) {
             $variants = ProductVariant::with('product')
                 ->whereHas('product', function ($query) use ($data) {
                     $query->where('title', 'like', '%' . $data . '%');
                 })
                 ->get();
         } else {
             $variants = ProductVariant::with('product')->get();
         }

         return response()->json([
             "success" => true,
             'variants' => $variants
         ]);

     }

     public function searchProductVariants(Request $request)
     {
         $data = $request->input("search");
         if (strlen(trim($data)) > 0) {
             $variants = ProductVariant::with('product')
                 ->whereHas('product', function ($query) use ($data) {
                     $query->where('title', 'like', '%' . $data . '%');
                 })
                 ->get();
         } else {
             $variants = ProductVariant::with('product')->get();
         }

         return response()->json([
             "success" => true,
             'variants' => $variants
         ]);
     }

     public function suggestProducts(Request $request)
     {
         $data = $request->input("search");

         if (strlen(trim($data)) > 0) {
             $products = Product::active()->where('title', 'like', '%' . $data . '%')->get();
             return response()->json([
                 "success" => true,
                 'products' => $products
             ]);
         } else {
            return response()->json([
               "success" => false,
            ]);
         }
     }

     public function suggestVariants(Request $request)
     {
         $id = $request->input("id");
         $variants = Product::find($id)->variants()->get();
         return response()->json([
            "success" => true,
            "variants" => $variants
         ]);
     }


}
