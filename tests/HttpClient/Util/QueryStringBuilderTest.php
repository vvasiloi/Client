<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\HttpClient\Util;

use Gitlab\HttpClient\Util\QueryStringBuilder;
use PHPUnit\Framework\TestCase;

class QueryStringBuilderTest extends TestCase
{
    /**
     * @dataProvider queryStringProvider
     *
     * @param array  $query
     * @param string $expected
     */
    public function testBuild(array $query, string $expected): void
    {
        $this->assertSame(\sprintf('?%s', $expected), QueryStringBuilder::build($query));
    }

    public function queryStringProvider()
    {
        //Indexed array.
        yield [
            ['iids' => [88, 86]],
            //iids[]=88&iids[]=86
            'iids%5B%5D=88&iids%5B%5D=86',
        ];

        //Non indexed array with only numeric keys.
        yield [
            ['iids' => [0 => 88, 2 => 86]],
            //iids[0]=88&iids[2]=86
            'iids%5B0%5D=88&iids%5B2%5D=86',
        ];

        yield [
            [
                'source_branch' => 'test_source',
                'target_branch' => 'test_master',
                'title' => 'test',
            ],
            'source_branch=test_source&target_branch=test_master&title=test',
        ];

        //Boolean encoding
        yield [
            ['push_events' => false, 'merge_requests_events' => 1],
            'push_events=0&merge_requests_events=1',
        ];

        //A deeply nested array.
        yield [
            [
                'search' => 'a project',
                'owned' => 'true',
                'iids' => [88, 86],
                'assoc' => [
                    'a' => 'b',
                    'c' => [
                        'd' => 'e',
                        'f' => 'g',
                    ],
                ],
                'nested' => [
                    'a' => [
                        [
                            'b' => 'c',
                        ],
                        [
                            'd' => 'e',
                            'f' => [
                                'g' => 'h',
                                'i' => 'j',
                                'k' => [87, 89],
                            ],
                        ],
                    ],
                ],
            ],
            //search=a project
            //&owned=true
            //&iids[]=88&iids[]=86
            //&assoc[a]=b&assoc[c][d]=e&assoc[c][f]=g
            //&nested[a][][b]=c&nested[a][][d]=e
            //&nested[a][][f][g]=h&nested[a][][f][i]=j
            //&nested[a][][f][k][]=87&nested[a][][f][k][]=89
            'search=a%20project&owned=true&iids%5B%5D=88&iids%5B%5D=86'.
            '&assoc%5Ba%5D=b&assoc%5Bc%5D%5Bd%5D=e&assoc%5Bc%5D%5Bf%5D=g'.
            '&nested%5Ba%5D%5B%5D%5Bb%5D=c&nested%5Ba%5D%5B%5D%5Bd%5D=e'.
            '&nested%5Ba%5D%5B%5D%5Bf%5D%5Bg%5D=h&nested%5Ba%5D%5B%5D%5Bf%5D%5Bi%5D=j'.
            '&nested%5Ba%5D%5B%5D%5Bf%5D%5Bk%5D%5B%5D=87&nested%5Ba%5D%5B%5D%5Bf%5D%5Bk%5D%5B%5D=89',
        ];
    }
}
