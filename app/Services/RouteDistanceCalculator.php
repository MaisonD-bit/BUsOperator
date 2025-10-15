<?php

namespace App\Services;

class RouteDistanceCalculator
{
    /**
     * Calculate total distance of a route from its geometry coordinates
     * Uses Haversine formula for accuracy
     * 
     * @param string|array $geometry GeoJSON string or array with coordinates
     * @return float Distance in kilometers
     */
    public static function calculateDistance($geometry): float
    {
        if (is_string($geometry)) {
            $geometry = json_decode($geometry, true);
        }

        if (!$geometry) {
            return 0.0;
        }

        // Handle two formats:
        // 1. Direct geometry object: { "type": "LineString", "coordinates": [...] }
        // 2. Feature collection: { "features": [{ "geometry": { "type": "LineString", "coordinates": [...] } }] }
        
        $coordinates = null;
        
        if (isset($geometry['type']) && $geometry['type'] === 'LineString' && isset($geometry['coordinates'])) {
            // Direct geometry object
            $coordinates = $geometry['coordinates'];
        } elseif (isset($geometry['features'][0]['geometry']['coordinates'])) {
            // Feature collection
            $coordinates = $geometry['features'][0]['geometry']['coordinates'];
        }
        
        if (!$coordinates || count($coordinates) < 2) {
            return 0.0;
        }

        $totalDistance = 0.0;

        // Calculate distance between each consecutive pair of coordinates
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $point1 = $coordinates[$i];
            $point2 = $coordinates[$i + 1];
            
            $totalDistance += self::haversineDistance(
                $point1[1], // lat1
                $point1[0], // lng1
                $point2[1], // lat2
                $point2[0]  // lng2
            );
        }

        return round($totalDistance, 2);
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * 
     * @param float $lat1 Latitude of point 1
     * @param float $lng1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lng2 Longitude of point 2
     * @return float Distance in kilometers
     */
    private static function haversineDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        // Calculate differences
        $latDiff = $lat2Rad - $lat1Rad;
        $lngDiff = $lng2Rad - $lng1Rad;

        // Haversine formula
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate distance from start and end coordinates only
     * 
     * @param string $startCoords "lat,lng" format
     * @param string $endCoords "lat,lng" format
     * @return float Distance in kilometers (straight line)
     */
    public static function calculateStraightLineDistance(string $startCoords, string $endCoords): float
    {
        $start = explode(',', $startCoords);
        $end = explode(',', $endCoords);

        if (count($start) !== 2 || count($end) !== 2) {
            return 0.0;
        }

        return round(self::haversineDistance(
            floatval($start[0]),
            floatval($start[1]),
            floatval($end[0]),
            floatval($end[1])
        ), 2);
    }
}
