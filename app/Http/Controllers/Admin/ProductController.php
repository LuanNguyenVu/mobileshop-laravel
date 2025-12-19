<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')->orderBy('id', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'product_image_file' => 'nullable|image|max:2048',
            'variants.*.color' => 'required',
            'variants.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload ảnh chính
            $imagePath = null;
            if ($request->hasFile('product_image_file')) {
                $file = $request->file('product_image_file');
                $fileName = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $fileName);
                $imagePath = 'uploads/products/' . $fileName;
            }

            // 2. Tạo Product
            $product = Product::create([
                'product_name' => $request->product_name,
                'brand' => $request->brand,
                'type' => $request->type,
                'product_image' => $imagePath,
                
                // Thông số ngắn
                'operating_system' => $request->operating_system,
                'screen' => $request->screen,
                'front_camera' => $request->front_camera,
                'camera' => $request->camera,
                'cpu' => $request->cpu,
                'gpu' => $request->gpu,
                'ram' => $request->ram,
                'rom' => $request->rom,
                'battery' => $request->battery,
                'rating' => $request->rating ?? 0,
                
                // HAI BÀI VIẾT RIÊNG BIỆT
                'detailed_specs' => $request->detailed_specs, // Bài viết thông số kỹ thuật
                'description' => $request->description,       // Bài viết mô tả sản phẩm
                
                'status' => 'in_stock'
            ]);

            // 3. Tạo Variants
            $this->processVariants($request, $product);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'product_name' => 'required',
            'product_image_file' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Product Info
            $data = $request->except(['variants', 'product_image_file']);
            
            // Xử lý ảnh chính
            if ($request->hasFile('product_image_file')) {
                if ($product->product_image && File::exists(public_path($product->product_image))) {
                    File::delete(public_path($product->product_image));
                }
                $file = $request->file('product_image_file');
                $fileName = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $fileName);
                $data['product_image'] = 'uploads/products/' . $fileName;
            }
            
            $product->update($data);

            // 2. Update Variants
            $this->processVariants($request, $product, true);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->product_image && File::exists(public_path($product->product_image))) {
            File::delete(public_path($product->product_image));
        }
        foreach ($product->variants as $variant) {
            if ($variant->image && File::exists(public_path($variant->image))) {
                File::delete(public_path($variant->image));
            }
        }
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm!');
    }

    // Hàm phụ trợ xử lý Variants (Dùng chung cho Store và Update để code gọn)
    private function processVariants(Request $request, Product $product, $isUpdate = false) {
        $submittedVariantIds = [];
        $totalStock = 0;

        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                if (empty($variantData['color'])) continue;

                // Upload ảnh biến thể
                $variantImagePath = null;
                if ($request->hasFile("variants.{$index}.image")) {
                    $file = $request->file("variants.{$index}.image");
                    $fileName = 'var_' . Str::slug($variantData['color']) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/variants'), $fileName);
                    $variantImagePath = 'uploads/variants/' . $fileName;
                }

                if ($isUpdate && isset($variantData['id']) && $variantData['id']) {
                    // Update
                    $variant = ProductVariant::find($variantData['id']);
                    if($variant) {
                        if ($variantImagePath) {
                            if ($variant->image && File::exists(public_path($variant->image))) {
                                File::delete(public_path($variant->image));
                            }
                            $variantData['image'] = $variantImagePath;
                        }
                        $variant->update($variantData);
                        $submittedVariantIds[] = $variant->id;
                    }
                } else {
                    // Create
                    if ($variantImagePath) $variantData['image'] = $variantImagePath;
                    $newVariant = $product->variants()->create($variantData);
                    $submittedVariantIds[] = $newVariant->id;
                }
                $totalStock += ($variantData['quantity'] ?? 0);
            }
        }

        // Xóa variant thừa khi update
        if ($isUpdate) {
            $variantsToDelete = $product->variants()->whereNotIn('id', $submittedVariantIds)->get();
            foreach ($variantsToDelete as $vDelete) {
                 if ($vDelete->image && File::exists(public_path($vDelete->image))) {
                    File::delete(public_path($vDelete->image));
                }
                $vDelete->delete();
            }
        }

        $product->update(['status' => $totalStock > 0 ? 'in_stock' : 'out_of_stock']);
    }
}