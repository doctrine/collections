#!/usr/bin/env bash
# Instruction-count comparison: bench/baseline (plain Collections) vs
# bench/reified (native reified generics, no defaults, concrete <int,Item>
# monomorph). Same fork binary; via perf_event_open (bench/perfcount).
#
# Because reified has NO defaults, construction syntax differs per branch, so
# each branch runs its OWN workload file (identical operations, only the
# `new ArrayCollection` line differs). Workloads are pinned to /tmp so they
# survive `git checkout`.
#
#   gcc -O2 -o bench/perfcount bench/perfcount.c
#   [PHP=...] [OPCACHE=on|off] [ITERS=] [SIZE=] [N=] bench/perfbench.sh
set -uo pipefail
REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP="${PHP:-/home/withinboredom/code/php-src/sapi/cli/php}"
PC="$REPO/bench/perfcount"
ITERS="${ITERS:-2000}"; SIZE="${SIZE:-500}"; N="${N:-5}"
cd "$REPO" || exit 1
[ -x "$PC" ] || { echo "build first: gcc -O2 -o bench/perfcount bench/perfcount.c" >&2; exit 1; }

case "${OPCACHE:-on}" in
  off) FLAGS=(-d opcache.enable_cli=0); OPMODE="opcache OFF" ;;
  *)   FLAGS=(-d opcache.enable_cli=1 -d opcache.jit=disable -d opcache.jit_buffer_size=0 -d opcache.validate_timestamps=1); OPMODE="opcache ON" ;;
esac

# Pin workloads outside the repo so branch switches can't delete them.
cp "$REPO/bench/collbench_baseline.php" /tmp/.cb_baseline.php
cp "$REPO/bench/collbench_reified.php"  /tmp/.cb_reified.php
export BENCH_AUTOLOAD="$REPO/vendor/autoload.php"

median() { sort -n | awk '{a[NR]=$1} END{print a[int((NR+1)/2)]}'; }

echo "### $OPMODE | ITERS=$ITERS SIZE=$SIZE | N=$N runs/branch" >&2
declare -A INS CHK
for spec in "baseline:bench/baseline:/tmp/.cb_baseline.php" "reified:bench/reified:/tmp/.cb_reified.php"; do
  label="${spec%%:*}"; rest="${spec#*:}"; ref="${rest%%:*}"; work="${rest#*:}"
  git checkout -q "$ref" || { echo "checkout $ref failed" >&2; exit 1; }
  "$PHP" "${FLAGS[@]}" -r 'opcache_reset();' >/dev/null 2>&1 || true
  "$PHP" "${FLAGS[@]}" "$work" "$ITERS" "$SIZE" >/dev/null 2>&1   # prime
  echo "=== $label ($(git rev-parse --short HEAD)) ===" >&2
  ins_list=()
  for i in $(seq 1 "$N"); do
    "$PC" "$PHP" "${FLAGS[@]}" "$work" "$ITERS" "$SIZE" >/tmp/.pcout 2>/tmp/.pcerr
    ins="$(grep -oE 'instructions=[0-9]+' /tmp/.pcerr | cut -d= -f2)"
    ins_list+=("$ins"); echo "  run $i: instructions=$ins" >&2
  done
  CHK["$label"]="$(grep -oE 'checksum=[0-9]+' /tmp/.pcout | cut -d= -f2)"
  INS["$label"]="$(printf '%s\n' "${ins_list[@]}" | median)"
done
git checkout -q bench/reified

echo "============================================================"
b="${INS[baseline]}"; r="${INS[reified]}"
echo "checksum baseline=${CHK[baseline]}  reified=${CHK[reified]}  $([ "${CHK[baseline]}" = "${CHK[reified]}" ] && echo MATCH || echo MISMATCH!!)"
awk -v b="$b" -v r="$r" 'BEGIN{ printf "median instructions  baseline=%d  reified=%d  delta=%+.3f%%\n", b, r, (r-b)/b*100 }'
echo "($OPMODE; restored to bench/reified)"
