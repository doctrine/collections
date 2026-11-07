<?php
// Reified-branch workload: ArrayCollection<int, Item> — a CONCRETE monomorph so
// the reified TKey/T checks actually fire on add/set/get/filter/etc.
// Identical operations to collbench_baseline.php; only the `new ArrayCollection`
// construction line differs (explicit ::<int, Item> args, no defaults).
//
//   php bench/collbench_reified.php [ITERS] [SIZE]
declare(strict_types=1);
require getenv('BENCH_AUTOLOAD') ?: __DIR__ . '/../vendor/autoload.php';

use Doctrine\Common\Collections\ArrayCollection;

final class Item
{
    public function __construct(public int $v)
    {
    }
}

$ITERS = (int) ($argv[1] ?? 2000);
$SIZE  = (int) ($argv[2] ?? 500);

/** @var array<int, Item> $data */
$data = [];
for ($i = 0; $i < $SIZE; $i++) {
    $data[$i] = new Item(($i * 7) % 101);
}

$checksum = 0;
for ($it = 0; $it < $ITERS; $it++) {
    $c = new ArrayCollection::<int, Item>($data);   // <-- only line that differs from baseline
    $c->add(new Item($it % 13));
    $c->set(0, new Item(1));
    $g  = $c->get(5);
    $ck = $c->containsKey(3);
    $ct = $c->contains($data[2]);
    $f  = $c->filter(static fn (Item $v, int $k): bool => $v->v % 2 === 0);
    $fr = $c->first();
    $la = $c->last();
    $sl = $c->slice(0, 10);
    [$m, $n] = $c->partition(static fn (int $k, Item $v): bool => $v->v > 50);

    $checksum += $c->count()
        + ($g instanceof Item ? $g->v : 0)
        + ($ck ? 1 : 0) + ($ct ? 1 : 0)
        + $f->count()
        + ($fr instanceof Item ? $fr->v : 0)
        + ($la instanceof Item ? $la->v : 0)
        + count($sl)
        + $m->count() + $n->count();
}

printf("class=%s  iters=%d size=%d  (checksum=%d)\n", (new ArrayCollection::<int, Item>())::class, $ITERS, $SIZE, $checksum);
