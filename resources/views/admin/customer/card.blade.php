<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Card</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for the card, overriding or complementing Tailwind for specific elements */
        body {
            font-family: 'Inter', sans-serif; /* Using Inter font as per instructions */
            background-color: #f1f3f5;
            margin: 0;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh; /* Ensure body takes full viewport height */
        }

        .card {
            width: 350px; /* Fixed width for the card */
            border-radius: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .logo {
            width: 60px;
            height: auto;
            margin-bottom: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .card-body p {
            margin: 10px 0;
            font-size: 15px;
        }

        .card-body p strong {
            width: 90px;
            display: inline-block;
            color: #333;
        }

        .barcode {
            margin-top: 20px;
            text-align: center;
        }

        /* Style for the barcode image */
        .barcode img {
            max-width: 100%;   /* Ensure image fits within its container */
            height: auto;      /* Maintain aspect ratio */
            display: block;    /* Ensures it behaves like a block element */
            margin: 0 auto;    /* Center the image */
            background-color: white; /* Ensure white background for barcode */
            padding: 5px 0;    /* Add some padding around the barcode */
        }

        /* Style for the card ID text below the barcode */
        .barcode-id-text {
            font-family: 'Inter', monospace; /* Monospace font for ID for clarity */
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 8px; /* Space between barcode and ID text */
            letter-spacing: 1px; /* Slightly increase letter spacing for readability */
        }


        .print-btn {
            margin-top: 20px;
            padding: 10px 25px;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .print-btn:hover {
            opacity: 0.9; /* Slight hover effect */
        }

        @media print {
            .print-btn {
                display: none; /* Hide print button when printing */
            }

            body {
                background-color: white; /* Ensure white background for print */
                padding: 0;
                margin: 0;
            }

            .card {
                box-shadow: none; /* Remove shadow for print */
                border: none;     /* Remove border for print */
                width: 100%;      /* Allow card to take full width of print page */
                max-width: 350px; /* Keep consistent size if needed, or remove for full page */
                margin: 0 auto;   /* Center on print page */
            }
        }
    </style>
</head>
<body>

    @php
        // These classes/models are assumed to be available in a Laravel/Blade environment
        use App\Models\Setting;
        use Milon\Barcode\DNS1D;

        // Fetch settings for primary and secondary colors, and logo path
        $setting = Setting::first();
        $primaryColor = $setting->primary_color ?? '#0d6efd'; // Default primary color (Bootstrap blue)
        $secondaryColor = $setting->secondary_color ?? '#6c757d'; // Default secondary color (Bootstrap gray)

        // Initialize DNS1D barcode generator
        $barcodeGenerator = new DNS1D();
        // Set storage path for barcodes (important for the library to function)
        $barcodeGenerator->setStorPath(storage_path('framework/barcodes'));

        // Prepare barcode image data
        $barcodeImage = '';
        // Assuming $customer is an object with a 'card_id' property
        // For demonstration, let's use a placeholder if $customer is not defined
        // In a real application, $customer would be passed from the controller
        $customer = $customer ?? (object)[
            'card_id' => 'CUST-686500CA5E6EB',
            'name' => 'Louis Shepard',
            'phone' => '+1 (448) 162-5069',
            'email' => 'syromypa@mailinator.com',
            'address' => 'Expedita aliqua Seq'
        ];

        if (!empty($customer->card_id)) {
            // Generate barcode as a PNG image (base64 encoded)
            // 'C128' is the barcode type, 1.4 is module size, 70 is height
            $barcodeImage = 'data:image/png;base64,' . $barcodeGenerator->getBarcodePNG($customer->card_id, 'C128', 1.4, 70);
        }

        // Placeholder for logo if not set in settings
        $logoPath = $setting?->logo_path ? asset('storage/' . $setting->logo_path) : 'https://placehold.co/60x60/0d6efd/ffffff?text=LOGO';
    @endphp

    <div id="printSection" class="w-full max-w-sm">
        <div class="card rounded-lg border border-gray-300 bg-white shadow-lg overflow-hidden">
            <div class="card-header rounded-t-lg p-4 text-center text-white font-bold text-xl flex flex-col items-center justify-center" style="background-color: {{ $primaryColor }};">
                <img src="{{ $logoPath }}" alt="Company Logo" class="logo rounded-full mb-2">
                <div>Customer Card</div>
            </div>

            <div class="card-body p-5">
                <p class="mb-2 text-base"><strong>Card ID:</strong> <span class="uppercase">{{ $customer->card_id }}</span></p>
                <p class="mb-2 text-base"><strong>Name:</strong> {{ $customer->name }}</p>
                <p class="mb-2 text-base"><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p class="mb-2 text-base"><strong>Email:</strong> {{ $customer->email }}</p>
                <p class="mb-2 text-base"><strong>Address:</strong> {{ $customer->address }}</p>

                {{-- Barcode as Image and Card ID Text --}}
                @if(!empty($barcodeImage))
                    <div class="barcode mt-5 text-center">
                        <img src="{{ $barcodeImage }}" alt="Customer Barcode">
                        {{-- Display card ID number below the barcode --}}
                        <p class="barcode-id-text">{{ $customer->card_id }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <button class="print-btn py-2 px-6 text-white font-bold rounded-md cursor-pointer transition-colors duration-300 ease-in-out mr-6" style="background-color: {{ $secondaryColor }};" onclick="printCard()">Print Card</button>

    <script>
        function printCard() {
            const printContents = document.getElementById('printSection').innerHTML;

            // Get the dynamic colors from the current document's computed styles
            const primaryColor = document.querySelector('.card-header').style.backgroundColor;
            const secondaryColor = document.querySelector('.print-btn').style.backgroundColor;

            // Styles to be applied to the print window
            const styles = `
                <style>
                    body {
                        font-family: 'Inter', sans-serif;
                        margin: 0;
                        padding: 0; /* No padding for print body */
                        background-color: white; /* Ensure white background */
                    }
                    .card {
                        width: 350px; /* Maintain card width for print */
                        border-radius: 10px;
                        border: 1px solid #ccc;
                        background-color: #fff;
                        box-shadow: none; /* No shadow in print */
                        overflow: hidden;
                        margin: 20px auto; /* Center card with some margin on print page */
                    }
                    .card-header {
                        background-color: ${primaryColor}; /* Use dynamic primary color */
                        color: white;
                        padding: 15px;
                        text-align: center;
                        font-size: 20px;
                        font-weight: bold;
                    }
                    .logo {
                        width: 60px;
                        height: auto;
                        margin-bottom: 10px;
                    }
                    .card-body {
                        padding: 20px;
                    }
                    .card-body p {
                        margin: 10px 0;
                        font-size: 15px;
                    }
                    .card-body p strong {
                        width: 90px;
                        display: inline-block;
                        color: #333;
                    }
                    .barcode {
                        margin-top: 20px;
                        text-align: center;
                    }
                    /* Crucial for barcode image visibility in print */
                    .barcode img {
                        max-width: 100%;
                        height: auto;
                        display: block;
                        margin: 0 auto;
                        background-color: white;
                        padding: 5px 0;
                    }
                    /* Ensure no print-specific styles interfere with barcode */
                    .barcode-id-text {
                        font-family: 'Inter', monospace; /* Monospace font for ID for clarity */
                        font-size: 14px;
                        font-weight: bold;
                        color: #333;
                        margin-top: 8px;
                        letter-spacing: 1px;
                    }
                </style>
            `;

            const win = window.open('', '', 'height=600,width=500');
            win.document.write('<html><head><title>Print Customer Card</title>');
            win.document.write(styles); // Inject the print-specific styles
            win.document.write('</head><body>');
            win.document.write(printContents); // Inject the content to be printed
            win.document.write('</body></html>');
            win.document.close(); // Close the document stream
            win.focus(); // Focus on the new window
            win.print(); // Open the print dialog
            // win.close(); // Optionally close the window after print dialog is shown
        }
    </script>

</body>
</html>