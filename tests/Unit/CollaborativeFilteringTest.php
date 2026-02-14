<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CollaborativeFilteringService;

class CollaborativeFilteringTest extends TestCase
{
    public function testExampleMatrix()
    {
        $svc = new CollaborativeFilteringService();

        // Example from the spec
        $ratings = [
            'U1' => ['A' => 5, 'B' => 3, 'C' => null, 'D' => 1],
            'U2' => ['A' => 4, 'B' => null, 'C' => null, 'D' => 1],
            'U3' => ['A' => 1, 'B' => 1, 'C' => null, 'D' => 5],
        ];

        $averages = $svc->computeUserAverages($ratings);
        $this->assertEqualsWithDelta(3.0, $averages['U1'], 0.0001);
        $this->assertEqualsWithDelta(2.5, $averages['U2'], 0.0001);
        $this->assertEqualsWithDelta(7.0/3.0, $averages['U3'], 0.0001);

        $normalized = $svc->normalizeRatings($ratings, $averages);
        $this->assertEqualsWithDelta(2.0, $normalized['U1']['A'], 0.0001);
        $this->assertEqualsWithDelta(0.0, $normalized['U1']['B'], 0.0001);
        $this->assertNull($normalized['U1']['C']);
        $this->assertEqualsWithDelta(-2.0, $normalized['U1']['D'], 0.0001);

        $this->assertEqualsWithDelta(1.5, $normalized['U2']['A'], 0.0001);
        $this->assertEqualsWithDelta(-1.5, $normalized['U2']['D'], 0.0001);

        $this->assertEqualsWithDelta(-1.3333333, $normalized['U3']['A'], 0.0001);
        $this->assertEqualsWithDelta(-1.3333333, $normalized['U3']['B'], 0.0001);
        $this->assertEqualsWithDelta(2.6666667, $normalized['U3']['D'], 0.0001);

        $sim = $svc->computeCenteredCosineSimilarity($normalized);

        // sim(U1,U2) approx 1.0 in example
        $this->assertEqualsWithDelta(1.0, $sim['U1']['U2'], 0.01);
        // sim(U1,U3) negative
        $this->assertLessThan(0.0, $sim['U1']['U3']);

        $neighbors = $svc->getTopNeighbors($sim, 1, true);
        $this->assertEquals('U2', $neighbors['U1'][0]['user']);
    }
}
