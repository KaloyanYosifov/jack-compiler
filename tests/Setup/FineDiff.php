<?php
/**
 * FINE granularity DIFF
 *
 * Computes a set of instructions to convert the content of
 * one string into another.
 *
 * Copyright (c) 2014-10-12 adaptations (see $num_changes and diffToArray()).
 * Copyright (c) 2014-10-11 corrections: https://github.com/ppKrauss/PHP-XmlDiff
 *   for corrections over https://github.com/gorhill/PHP-FineDiff/pulls
 *   see also requested issues for mulibyte (UTF8 safe!).
 * Copy 2014-10-10 from https://github.com/gorhill/PHP-FineDiff/blob/master/finediff.php
 * Copyright (c) 2011 Raymond Hill (http://raymondhill.net/blog/?p=441)
 *
 * Licensed under The MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @copyright Copyright 2011 (c) Raymond Hill (http://raymondhill.net/blog/?p=441)
 * @version 0.7 (adaptaed from 0.6)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Usage (simplest):
 *
 *   include 'finediff.php';
 *
 *   // for the stock stack, granularity values are:
 *   // FineDiff::$paragraphGranularity = paragraph/line level
 *   // FineDiff::$sentenceGranularity = sentence level
 *   // FineDiff::$wordGranularity = word level
 *   // FineDiff::$characterGranularity = character level [default]
 *
 *   $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text [, $granularityStack = null] );
 *   // store opcodes for later use...
 *
 *   ...
 *
 *   // restore $to_text from $from_text + $opcodes
 *   include 'finediff.php';
 *   $to_text = FineDiff::renderToTextFromOpcodes($from_text, $opcodes);
 *
 *   ...
 */

/**
 * Persisted opcodes (string) are a sequence of atomic opcode.
 * A single opcode can be one of the following:
 *   c | c{n} | d | d{n} | i:{c} | i{length}:{s}
 *   'c'        = copy one character from source
 *   'c{n}'     = copy n characters from source
 *   'd'        = skip one character from source
 *   'd{n}'     = skip n characters from source
 *   'i:{c}     = insert character 'c'
 *   'i{n}:{s}' = insert string s, which is of length n
 *
 * Do not exist as of now, under consideration:
 *   'm{n}:{o}  = move n characters from source o characters ahead.
 *   It would be essentially a shortcut for a delete->copy->insert
 *   command (swap) for when the inserted segment is exactly the same
 *   as the deleted one, and with only a copy operation in between.
 *   TODO: How often this case occurs? Is it worth it? Can only
 *   be done as a postprocessing method (->optimize()?)
 */

namespace Tests\Setup;

abstract class FineDiffOp
{
    abstract public function getFromLen();

    abstract public function getToLen();

    abstract public function getOpcode();
}

class FineDiffDeleteOp extends FineDiffOp
{
    public function __construct($len)
    {
        $this->fromLen = $len;
    }

    public function getFromLen()
    {
        return $this->fromLen;
    }

    public function getToLen()
    {
        return 0;
    }

    public function getOpcode()
    {
        if ($this->fromLen === 1) {
            return 'd';
        }

        return "d{$this->fromLen}";
    }
}

class FineDiffInsertOp extends FineDiffOp
{
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function getFromLen()
    {
        return 0;
    }

    public function getToLen()
    {
        return mb_strlen($this->text);
    }

    public function getText()
    {
        return $this->text;
    }

    public function getOpcode()
    {
        $to_len = mb_strlen($this->text);
        if ($to_len === 1) {
            return "i:{$this->text}";
        }

        return "i{$to_len}:{$this->text}";
    }
}

class FineDiffReplaceOp extends FineDiffOp
{
    public function __construct($fromLen, $text)
    {
        $this->fromLen = $fromLen;
        $this->text = $text;
    }

    public function getFromLen()
    {
        return $this->fromLen;
    }

    public function getToLen()
    {
        return mb_strlen($this->text);
    }

    public function getText()
    {
        return $this->text;
    }

    public function getOpcode()
    {
        if ($this->fromLen === 1) {
            $del_opcode = 'd';
        } else {
            $del_opcode = "d{$this->fromLen}";
        }
        $to_len = mb_strlen($this->text);
        if ($to_len === 1) {
            return "{$del_opcode}i:{$this->text}";
        }

        return "{$del_opcode}i{$to_len}:{$this->text}";
    }
}

class FineDiffCopyOp extends FineDiffOp
{
    public function __construct($len)
    {
        $this->len = $len;
    }

    public function getFromLen()
    {
        return $this->len;
    }

    public function getToLen()
    {
        return $this->len;
    }

    public function getOpcode()
    {
        if ($this->len === 1) {
            return 'c';
        }

        return "c{$this->len}";
    }

    public function increase($size)
    {
        return $this->len += $size;
    }
}

/**
 * FineDiff ops
 *
 * Collection of ops
 */
class FineDiffOps
{
    public $edits = [];

    public function appendOpcode($opcode, $from, $from_offset, $from_len)
    {
        if ($opcode === 'c') {
            $edits[] = new FineDiffCopyOp($from_len);
        } elseif ($opcode === 'd') {
            $edits[] = new FineDiffDeleteOp($from_len);
        } else /* if ( $opcode === 'i' ) */ {
            $edits[] = new FineDiffInsertOp(mb_substr($from, $from_offset, $from_len));
        }
    }
}

/**
 * FineDiff class
 *
 * TODO: Document
 *
 */
class FineDiff
{
    /**
     * Stock granularity stacks and delimiters
     */

    public const paragraphDelimiters = "\n\r";
    public const sentenceDelimiters = ".\n\r";
    public const wordDelimiters = " \t.\n\r";
    public const characterDelimiters = '';

    /**------------------------------------------------------------------------
     *
     * Public section
     *
     */
    public $num_changes = null;  // new v0.7
    public static $paragraphGranularity = [
        FineDiff::paragraphDelimiters,
    ];
    public static $sentenceGranularity = [
        FineDiff::paragraphDelimiters,
        FineDiff::sentenceDelimiters,
    ];
    public static $wordGranularity = [
        FineDiff::paragraphDelimiters,
        FineDiff::sentenceDelimiters,
        FineDiff::wordDelimiters,
    ];
    public static $characterGranularity = [
        FineDiff::paragraphDelimiters,
        FineDiff::sentenceDelimiters,
        FineDiff::wordDelimiters,
        FineDiff::characterDelimiters,
    ];

    public static $textStack = [
        '.',
        " \t.\n\r",
        '',
    ];

    /**
     * Constructor
     * ...
     * The $granularityStack allows FineDiff to be configurable so that
     * a particular stack tailored to the specific content of a document can
     * be passed.
     */
    public function __construct($from_text = '', $to_text = '', $granularityStack = null)
    {
        mb_internal_encoding('UTF-8');
        // setup stack for generic text documents by default
        $this->granularityStack = $granularityStack ? $granularityStack : FineDiff::$characterGranularity;
        $this->edits = [];
        $this->from_text = $from_text;
        $this->doDiff($from_text, $to_text);
    }

    public function getOps()
    {
        return $this->edits;
    }

    public function getOpcodes()
    {
        $opcodes = [];
        foreach ($this->edits as $edit) {
            $opcodes[] = $edit->getOpcode();
        }

        return implode('', $opcodes);
    }

    /**
     * diffToArray, mount array from edits.
     * @param $splitBlock boolean (FALSE), true for split string into many items.
     * @param $useTrim boolean (TRUE), true for trim lines.
     * @return array (commands 'cpy'/'del'/'ins', line) or ('rpl', line_del, line_ins)
     * NOTE: add in v0.7, 2014-10-10 by ppkrauss for XML
     */
    public function diffToArray($splitBlock = false, $useTrim = true)
    {
        $splitBlock = false;
        $useTrim = true; // constant parameters, no risk
        $ret = [];
        $in_offset = 0;
        $nChanges = 0;
        foreach ($this->edits as $edit) {
            $n = $edit->getFromLen();
            if ($edit instanceof FineDiffCopyOp) {
                $s = mb_substr($this->from_text, $in_offset, $n);
                $aux = ['cpy', trim($s)];
            } elseif ($edit instanceof FineDiffDeleteOp) {
                $s = mb_substr($this->from_text, $in_offset, $n);
                $aux = ['del', trim($s)];
                $nChanges++;
            } elseif ($edit instanceof FineDiffInsertOp) {
                $s = mb_substr($edit->getText(), 0, $edit->getToLen());
                $aux = ['ins', trim($s)];
                $nChanges++;
            } else { //   $edit instanceof FineDiffReplaceOp
                $s_del = mb_substr($this->from_text, $in_offset, $n);
                $s_ins = mb_substr($edit->getText(), 0, $edit->getToLen());
                $aux = ['rpl', trim($s_del), trim($s_ins)];
                $nChanges++;
            }
            if ($splitBlock) { // must review for aux[2]
                $allLines = explode("\n", $aux[1]); // OPS, and about rpl's $aux[2]?
                if (count($allLines) > 1) {
                    foreach ($allLines as $line) {
                        $ret[] = ["$aux[0]-blk", $line];
                    }
                } else {
                    $ret[] = $aux;
                }
            } else {
                $ret[] = $aux;
            }
            $in_offset += $n;
        }
        $this->num_changes = $nChanges;

        return $ret;
    }

    public function renderDiffToHTML()
    {
        $in_offset = 0;
        $nChanges = 0;
        ob_start();
        foreach ($this->edits as $edit) {
            $n = $edit->getFromLen();
            if ($edit instanceof FineDiffCopyOp) {
                FineDiff::renderDiffToHTMLFromOpcode('c', $this->from_text, $in_offset, $n);
            } elseif ($edit instanceof FineDiffDeleteOp) {
                FineDiff::renderDiffToHTMLFromOpcode('d', $this->from_text, $in_offset, $n);
                $nChanges++;
            } elseif ($edit instanceof FineDiffInsertOp) {
                FineDiff::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen());
                $nChanges++;
            } else /* if ( $edit instanceof FineDiffReplaceOp ) */ {
                FineDiff::renderDiffToHTMLFromOpcode('d', $this->from_text, $in_offset, $n);
                FineDiff::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen());
                $nChanges++;
            }
            $in_offset += $n;
        }
        $this->num_changes = $nChanges;    // new in v0.7

        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Return an opcodes string describing the diff between a "From" and a
     * "To" string
     */
    public static function getDiffOpcodes($from, $to, $granularities = null)
    {
        $diff = new FineDiff($from, $to, $granularities);

        return $diff->getOpcodes();
    }

    /**------------------------------------------------------------------------
     * Return an iterable collection of diff ops from an opcodes string
     */
    public static function getDiffOpsFromOpcodes($opcodes)
    {
        $diffops = new FineDiffOps();
        FineDiff::renderFromOpcodes(null, $opcodes, [$diffops, 'appendOpcode']);

        return $diffops->edits;
    }

    /**------------------------------------------------------------------------
     * Re-create the "To" string from the "From" string and an "Opcodes" string
     */
    public static function renderToTextFromOpcodes($from, $opcodes)
    {
        ob_start();
        FineDiff::renderFromOpcodes($from, $opcodes, ['FineDiff', 'renderToTextFromOpcode']);

        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Render the diff to an HTML string
     */
    public static function renderDiffToHTMLFromOpcodes($from, $opcodes)
    {
        ob_start();
        FineDiff::renderFromOpcodes($from, $opcodes, ['FineDiff', 'renderDiffToHTMLFromOpcode']);

        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Generic opcodes parser, user must supply callback for handling
     * single opcode
     */
    public static function renderFromOpcodes($from, $opcodes, $callback)
    {
        if (!is_callable($callback)) {
            return;
        }
        $opcodes_len = mb_strlen($opcodes);
        $from_offset = $opcodes_offset = 0;
        while ($opcodes_offset < $opcodes_len) {
            $opcode = mb_substr($opcodes, $opcodes_offset, 1);
            $opcodes_offset++;
            $n = intval(mb_substr($opcodes, $opcodes_offset));
            if ($n) {
                $opcodes_offset += mb_strlen(strval($n));
            } else {
                $n = 1;
            }
            if ($opcode === 'c') { // copy n characters from source
                call_user_func($callback, 'c', $from, $from_offset, $n, '');
                $from_offset += $n;
            } elseif ($opcode === 'd') { // delete n characters from source
                call_user_func($callback, 'd', $from, $from_offset, $n, '');
                $from_offset += $n;
            } else /* if ( $opcode === 'i' ) */ { // insert n characters from opcodes
                call_user_func($callback, 'i', $opcodes, $opcodes_offset + 1, $n);
                $opcodes_offset += 1 + $n;
            }
        }
    }

    /**------------------------------------------------------------------------
     *
     * Private section
     *
     */

    /**
     * Entry point to compute the diff.
     */
    private function doDiff($from_text, $to_text)
    {
        $this->last_edit = false;
        $this->stackpointer = 0;
        $this->from_text = $from_text;
        $this->from_offset = 0;
        // can't diff without at least one granularity specifier
        if (empty($this->granularityStack)) {
            return;
        }
        $this->_processGranularity($from_text, $to_text);
    }

    /**
     * This is the recursive function which is responsible for
     * handling/increasing granularity.
     *
     * Incrementally increasing the granularity is key to compute the
     * overall diff in a very efficient way.
     */
    private function _processGranularity($from_segment, $to_segment)
    {
        $delimiters = $this->granularityStack[$this->stackpointer++];
        $has_next_stage = $this->stackpointer < count($this->granularityStack);
        foreach (FineDiff::doFragmentDiff($from_segment, $to_segment, $delimiters) as $fragment_edit) {
            // increase granularity
            if ($fragment_edit instanceof FineDiffReplaceOp && $has_next_stage) {
                $this->_processGranularity(
                    mb_substr($this->from_text, $this->from_offset, $fragment_edit->getFromLen()),
                    $fragment_edit->getText()
                );
            } // fuse copy ops whenever possible
            elseif ($fragment_edit instanceof FineDiffCopyOp && $this->last_edit instanceof FineDiffCopyOp) {
                $this->edits[count($this->edits) - 1]->increase($fragment_edit->getFromLen());
                $this->from_offset += $fragment_edit->getFromLen();
            } else {
                /* $fragment_edit instanceof FineDiffCopyOp */
                /* $fragment_edit instanceof FineDiffDeleteOp */
                /* $fragment_edit instanceof FineDiffInsertOp */
                $this->edits[] = $this->last_edit = $fragment_edit;
                $this->from_offset += $fragment_edit->getFromLen();
            }
        }
        $this->stackpointer--;
    }

    /**
     * This is the core algorithm which actually perform the diff itself,
     * fragmenting the strings as per specified delimiters.
     *
     * This function is naturally recursive, however for performance purpose
     * a local job queue is used instead of outright recursivity.
     */
    private static function doFragmentDiff($from_text, $to_text, $delimiters)
    {
        // Empty delimiter means character-level diffing.
        // In such case, use code path optimized for character-level
        // diffing.
        if (empty($delimiters)) {
            return FineDiff::doCharDiff($from_text, $to_text);
        }

        $result = [];

        // fragment-level diffing
        $from_text_len = mb_strlen($from_text);
        $to_text_len = mb_strlen($to_text);
        $from_fragments = FineDiff::extractFragments($from_text, $delimiters);
        $to_fragments = FineDiff::extractFragments($to_text, $delimiters);

        $jobs = [[0, $from_text_len, 0, $to_text_len]];

        $cached_array_keys = [];

        while ($job = array_pop($jobs)) {
            // get the segments which must be diff'ed
            [$from_segment_start, $from_segment_end, $to_segment_start, $to_segment_end] = $job;

            // catch easy cases first
            $from_segment_length = $from_segment_end - $from_segment_start;
            $to_segment_length = $to_segment_end - $to_segment_start;
            if (!$from_segment_length || !$to_segment_length) {
                if ($from_segment_length) {
                    $result[$from_segment_start * 4] = new FineDiffDeleteOp($from_segment_length);
                } elseif ($to_segment_length) {
                    $result[$from_segment_start * 4 + 1] = new FineDiffInsertOp(mb_substr($to_text, $to_segment_start, $to_segment_length));
                }
                continue;
            }

            // find longest copy operation for the current segments
            $best_copy_length = 0;

            $from_base_fragment_index = $from_segment_start;

            $cached_array_keys_for_current_segment = [];

            while ($from_base_fragment_index < $from_segment_end) {
                $from_base_fragment = $from_fragments[$from_base_fragment_index];
                $from_base_fragment_length = mb_strlen($from_base_fragment);
                // performance boost: cache array keys
                if (!isset($cached_array_keys_for_current_segment[$from_base_fragment])) {
                    if (!isset($cached_array_keys[$from_base_fragment])) {
                        $to_all_fragment_indices = $cached_array_keys[$from_base_fragment] = array_keys(
                            $to_fragments,
                            $from_base_fragment,
                            true
                        );
                    } else {
                        $to_all_fragment_indices = $cached_array_keys[$from_base_fragment];
                    }
                    // get only indices which falls within current segment
                    if ($to_segment_start > 0 || $to_segment_end < $to_text_len) {
                        $to_fragment_indices = [];
                        foreach ($to_all_fragment_indices as $to_fragment_index) {
                            if ($to_fragment_index < $to_segment_start) {
                                continue;
                            }
                            if ($to_fragment_index >= $to_segment_end) {
                                break;
                            }
                            $to_fragment_indices[] = $to_fragment_index;
                        }
                        $cached_array_keys_for_current_segment[$from_base_fragment] = $to_fragment_indices;
                    } else {
                        $to_fragment_indices = $to_all_fragment_indices;
                    }
                } else {
                    $to_fragment_indices = $cached_array_keys_for_current_segment[$from_base_fragment];
                }
                // iterate through collected indices
                foreach ($to_fragment_indices as $to_base_fragment_index) {
                    $fragment_index_offset = $from_base_fragment_length;
                    // iterate until no more match
                    for (; ;) {
                        $fragment_from_index = $from_base_fragment_index + $fragment_index_offset;
                        if ($fragment_from_index >= $from_segment_end) {
                            break;
                        }
                        $fragment_to_index = $to_base_fragment_index + $fragment_index_offset;
                        if ($fragment_to_index >= $to_segment_end) {
                            break;
                        }
                        if ($from_fragments[$fragment_from_index] !== $to_fragments[$fragment_to_index]) {
                            break;
                        }
                        $fragment_length = mb_strlen($from_fragments[$fragment_from_index]);
                        $fragment_index_offset += $fragment_length;
                    }
                    if ($fragment_index_offset > $best_copy_length) {
                        $best_copy_length = $fragment_index_offset;
                        $best_from_start = $from_base_fragment_index;
                        $best_to_start = $to_base_fragment_index;
                    }
                }
                $from_base_fragment_index += mb_strlen($from_base_fragment);
                // If match is larger than half segment size, no point trying to find better
                // TODO: Really?
                if ($best_copy_length >= $from_segment_length / 2) {
                    break;
                }
                // no point to keep looking if what is left is less than
                // current best match
                if ($from_base_fragment_index + $best_copy_length >= $from_segment_end) {
                    break;
                }
            }

            if ($best_copy_length) {
                $jobs[] = [$from_segment_start, $best_from_start, $to_segment_start, $best_to_start];
                $result[$best_from_start * 4 + 2] = new FineDiffCopyOp($best_copy_length);
                $jobs[] = [$best_from_start + $best_copy_length, $from_segment_end, $best_to_start + $best_copy_length, $to_segment_end];
            } else {
                $result[$from_segment_start * 4] = new FineDiffReplaceOp(
                    $from_segment_length,
                    mb_substr($to_text, $to_segment_start, $to_segment_length)
                );
            }
        }

        ksort($result, SORT_NUMERIC);

        return array_values($result);
    }

    /**
     * Perform a character-level diff.
     *
     * The algorithm is quite similar to doFragmentDiff(), except that
     * the code path is optimized for character-level diff -- mb_strpos() is
     * used to find out the longest common subequence of characters.
     *
     * We try to find a match using the longest possible subsequence, which
     * is at most the length of the shortest of the two strings, then incrementally
     * reduce the size until a match is found.
     *
     * I still need to study more the performance of this function. It
     * appears that for long strings, the generic doFragmentDiff() is more
     * performant. For word-sized strings, doCharDiff() is somewhat more
     * performant.
     */
    private static function doCharDiff($from_text, $to_text)
    {
        $result = [];
        $jobs = [[0, mb_strlen($from_text), 0, mb_strlen($to_text)]];
        while ($job = array_pop($jobs)) {
            // get the segments which must be diff'ed
            [$from_segment_start, $from_segment_end, $to_segment_start, $to_segment_end] = $job;
            $from_segment_len = $from_segment_end - $from_segment_start;
            $to_segment_len = $to_segment_end - $to_segment_start;

            // catch easy cases first
            if (!$from_segment_len || !$to_segment_len) {
                if ($from_segment_len) {
                    $result[$from_segment_start * 4 + 0] = new FineDiffDeleteOp($from_segment_len);
                } elseif ($to_segment_len) {
                    $result[$from_segment_start * 4 + 1] = new FineDiffInsertOp(mb_substr($to_text, $to_segment_start, $to_segment_len));
                }
                continue;
            }
            if ($from_segment_len >= $to_segment_len) {
                $copy_len = $to_segment_len;
                while ($copy_len) {
                    $to_copy_start = $to_segment_start;
                    $to_copy_start_max = $to_segment_end - $copy_len;
                    while ($to_copy_start <= $to_copy_start_max) {
                        $from_copy_start = mb_strpos(
                            mb_substr($from_text, $from_segment_start, $from_segment_len),
                            mb_substr($to_text, $to_copy_start, $copy_len)
                        );
                        if ($from_copy_start !== false) {
                            $from_copy_start += $from_segment_start;
                            break 2;
                        }
                        $to_copy_start++;
                    }
                    $copy_len--;
                }
            } else {
                $copy_len = $from_segment_len;
                while ($copy_len) {
                    $from_copy_start = $from_segment_start;
                    $from_copy_start_max = $from_segment_end - $copy_len;
                    while ($from_copy_start <= $from_copy_start_max) {
                        $to_copy_start = mb_strpos(
                            mb_substr($to_text, $to_segment_start, $to_segment_len),
                            mb_substr($from_text, $from_copy_start, $copy_len)
                        );
                        if ($to_copy_start !== false) {
                            $to_copy_start += $to_segment_start;
                            break 2;
                        }
                        $from_copy_start++;
                    }
                    $copy_len--;
                }
            }
            // match found
            if ($copy_len) {
                $jobs[] = [$from_segment_start, $from_copy_start, $to_segment_start, $to_copy_start];
                $result[$from_copy_start * 4 + 2] = new FineDiffCopyOp($copy_len);
                $jobs[] = [$from_copy_start + $copy_len, $from_segment_end, $to_copy_start + $copy_len, $to_segment_end];
            } // no match,  so delete all, insert all
            else {
                $result[$from_segment_start * 4] = new FineDiffReplaceOp(
                    $from_segment_len,
                    mb_substr($to_text, $to_segment_start, $to_segment_len)
                );
            }
        }
        ksort($result, SORT_NUMERIC);

        return array_values($result);
    }

    /**
     * Efficiently fragment the text into an array according to
     * specified delimiters.
     * No delimiters means fragment into single character.
     * The array indices are the offset of the fragments into
     * the input string.
     * A sentinel empty fragment is always added at the end.
     * Careful: No check is performed as to the validity of the
     * delimiters.
     */
    private static function extractFragments($text, $delimiters)
    {
        // special case: split into characters
        if (empty($delimiters)) {
            $chars = preg_split('/(?<!^)(?!$)/u', $text); // see http://stackoverflow.com/a/26328237/287948
            // is an UTF8-safe str_split($text, 1);
            $chars[mb_strlen($text)] = ''; // check if adding, can optimize removing /(?!$)/ from regex

            return $chars;
        }
        $fragments = [];
        $start = $end = 0;
        for (; ;) {
            $end += strcspn($text, $delimiters, $end); // UTF8 safe??
            $end += strspn($text, $delimiters, $end);  // UTF8 safe??
            if ($end === $start) {
                break;
            }
            $fragments[$start] = mb_substr($text, $start, $end - $start);
            $start = $end;
        }
        $fragments[$start] = '';

        return $fragments;
    }

    /**
     * Stock opcode renderers
     */
    private static function renderToTextFromOpcode($opcode, $from, $from_offset, $from_len)
    {
        if ($opcode === 'c' || $opcode === 'i') {
            echo mb_substr($from, $from_offset, $from_len);
        }
    }

    private static function renderDiffToHTMLFromOpcode($opcode, $from, $from_offset, $from_len)
    {
        if ($opcode === 'c') {
            echo htmlentities(mb_substr($from, $from_offset, $from_len));
        } elseif ($opcode === 'd') {
            $deletion = mb_substr($from, $from_offset, $from_len);
            if (strcspn($deletion, " \n\r") === 0) {
                $deletion = str_replace(["\n", "\r"], ['\n', '\r'], $deletion);
            }
            echo '<del>', htmlentities($deletion), '</del>';
        } else /* if ( $opcode === 'i' ) */ {
            echo '<ins>', htmlentities(mb_substr($from, $from_offset, $from_len)), '</ins>';
        }
    }
}
