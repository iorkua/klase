<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Title Document Verification</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center py-10">
  <div class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-lg border border-gray-300">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <img src="http://klas.com.ng/assets/logo/logo2.jpg" alt="KANGIS Logo" class="w-16 h-16 object-contain" />
      <div class="text-sm text-gray-600 font-semibold">{{ $application->file_number ?? '' }}</div>
    </div>

    <h1 class="text-xl font-bold text-center text-green-700 uppercase">
      Title Document Verification
    </h1>
    <p class="text-center text-gray-700 text-sm">
      Verify the title document submitted for recertification, please
    </p>

    <!-- First Section -->
    <div class="mt-6">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-gray-700 font-bold">Name:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
        <div>
          <label class="block text-gray-700 font-bold">Rank:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-gray-700 font-bold">Signature:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
        <div>
          <label class="block text-gray-700 font-bold">Date:</label>
          <input type="text" value=""  disabled  class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-gray-700 font-bold">KANGIS Number:</label>
          <input type="text" value=""  disabled  class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
        <div>
          <label class="block text-gray-700 font-bold">Land File Number:</label>
          <input type="text" value=""  disabled  class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
      </div>
    </div>

    <!-- Second Section -->
    <div class="mt-8 border-t border-gray-300 pt-6">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-gray-700 font-bold">Name:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
        <div>
          <label class="block text-gray-700 font-bold">Rank:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-gray-700 font-bold">Sign:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
        <div>
          <label class="block text-gray-700 font-bold">Date:</label>
          <input type="text" value="" disabled class="w-full border-b border-gray-400 focus:outline-none bg-gray-100 text-gray-500 cursor-not-allowed" />
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center text-gray-500 text-sm font-semibold">
      SERVICES CENTER
    </div>
  </div>
</body>
</html>
