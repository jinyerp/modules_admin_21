<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
    {{ Livewire::styles() }}
</head>
<body>
    <h1>Test Page Working!</h1>
    <p>Controller Class: {{ isset($jsonData['controller']) ? $jsonData['controller'] : 'Not set' }}</p>
    <p>Title: {{ $title ?? 'Not set' }}</p>
    <p>About to load Livewire component...</p>
    
    <div class="test-component">
        @php
            try {
        @endphp
            @livewire('jiny-admin2::admin-table')
        @php
            } catch (\Exception $e) {
                echo "<p>Error loading component: " . $e->getMessage() . "</p>";
            }
        @endphp
    </div>
    
    {{ Livewire::scripts() }}
</body>
</html>