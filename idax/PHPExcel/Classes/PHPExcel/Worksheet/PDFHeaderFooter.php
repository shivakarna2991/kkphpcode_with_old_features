<?php
/**
 * PHPExcel_Worksheet_HeaderFooter
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not,241 write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 *
 * <code>
 * Header/Footer Formatting Syntax taken from Office Open XML Part 4 - Markup Language Reference, page 1970:
 *
 * There are a number of formatting codes that can be written inline with the actual header / footer text, which
 * affect the formatting in the header or footer.
 *
 * Example: This example shows the text "Center Bold Header" on the first line (center section), and the date on
 * the second line (center section).
 *         &CCenter &"-,Bold"Bold&"-,Regular"Header_x000A_&D
 *
 * General Rules:
 * Use standard html code in a div.
 * </code>
 *
 */
class PHPExcel_Worksheet_PDFHeaderFooter
{
    /* Header/footer image location */
    const IMAGE_HEADER_LEFT   = 'LH';
    const IMAGE_HEADER_CENTER = 'CH';
    const IMAGE_HEADER_RIGHT  = 'RH';
    const IMAGE_FOOTER_LEFT   = 'LF';
    const IMAGE_FOOTER_CENTER = 'CF';
    const IMAGE_FOOTER_RIGHT  = 'RF';

    /**
     * OddHeader
     *
     * @var string
     */
    private $oddHeader = '';

    /**
     * OddFooter
     *
     * @var string
     */
    private $oddFooter = '';

    /**
     * EvenHeader
     *
     * @var string
     */
    private $evenHeader = '';

    /**
     * EvenFooter
     *
     * @var string
     */
    private $evenFooter = '';

    /**
     * FirstHeader
     *
     * @var string
     */
    private $firstHeader = '';

    /**
     * FirstFooter
     *
     * @var string
     */
    private $firstFooter = '';

    /**
     * Different header for Odd/Even, defaults to false
     *
     * @var boolean
     */
    private $differentOddEven = false;

    /**
     * Different header for first page, defaults to false
     *
     * @var boolean
     */
    private $differentFirst = false;

    /**
     * Scale with document, defaults to true
     *
     * @var boolean
     */
    private $scaleWithDocument = true;

    /**
     * Align with margins, defaults to true
     *
     * @var boolean
     */
    private $alignWithMargins = true;

    /**
     * Header/footer images
     *
     * @var PHPExcel_Worksheet_HeaderFooterDrawing[]
     */
    private $headerFooterImages = array();

    /**
     * Create a new PHPExcel_Worksheet_HeaderFooter
     */
    public function __construct()
    {
    }

    /**
     * Get OddHeader
     *
     * @return string
     */
    public function getOddHeader()
    {
        return $this->oddHeader;
    }

    /**
     * Set OddHeader
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setOddHeader($pValue)
    {
        $this->oddHeader = $pValue;
        return $this;
    }

    /**
     * Get OddFooter
     *
     * @return string
     */
    public function getOddFooter()
    {
        return $this->oddFooter;
    }

    /**
     * Set OddFooter
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setOddFooter($pValue)
    {
        $this->oddFooter = $pValue;
        return $this;
    }

    /**
     * Get EvenHeader
     *
     * @return string
     */
    public function getEvenHeader()
    {
        return $this->evenHeader;
    }

    /**
     * Set EvenHeader
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setEvenHeader($pValue)
    {
        $this->evenHeader = $pValue;
        return $this;
    }

    /**
     * Get EvenFooter
     *
     * @return string
     */
    public function getEvenFooter()
    {
        return $this->evenFooter;
    }

    /**
     * Set EvenFooter
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setEvenFooter($pValue)
    {
        $this->evenFooter = $pValue;
        return $this;
    }

    /**
     * Get FirstHeader
     *
     * @return string
     */
    public function getFirstHeader()
    {
        return $this->firstHeader;
    }

    /**
     * Set FirstHeader
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setFirstHeader($pValue)
    {
        $this->firstHeader = $pValue;
        return $this;
    }

    /**
     * Get FirstFooter
     *
     * @return string
     */
    public function getFirstFooter()
    {
        return $this->firstFooter;
    }

    /**
     * Set FirstFooter
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setFirstFooter($pValue)
    {
        $this->firstFooter = $pValue;
        return $this;
    }

    /**
     * Get DifferentOddEven
     *
     * @return boolean
     */
    public function getDifferentOddEven()
    {
        return $this->differentOddEven;
    }

    /**
     * Set DifferentOddEven
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setDifferentOddEven($pValue = false)
    {
        $this->differentOddEven = $pValue;
        return $this;
    }

    /**
     * Get DifferentFirst
     *
     * @return boolean
     */
    public function getDifferentFirst()
    {
        return $this->differentFirst;
    }

    /**
     * Set DifferentFirst
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setDifferentFirst($pValue = false)
    {
        $this->differentFirst = $pValue;
        return $this;
    }

    /**
     * Get ScaleWithDocument
     *
     * @return boolean
     */
    public function getScaleWithDocument()
    {
        return $this->scaleWithDocument;
    }

    /**
     * Set ScaleWithDocument
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setScaleWithDocument($pValue = true)
    {
        $this->scaleWithDocument = $pValue;
        return $this;
    }

    /**
     * Get AlignWithMargins
     *
     * @return boolean
     */
    public function getAlignWithMargins()
    {
        return $this->alignWithMargins;
    }

    /**
     * Set AlignWithMargins
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setAlignWithMargins($pValue = true)
    {
        $this->alignWithMargins = $pValue;
        return $this;
    }

    /**
     * Add header/footer image
     *
     * @param PHPExcel_Worksheet_HeaderFooterDrawing $image
     * @param string $location
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function addImage(PHPExcel_Worksheet_HeaderFooterDrawing $image = null, $location = self::IMAGE_HEADER_LEFT)
    {
        $this->headerFooterImages[$location] = $image;
        return $this;
    }

    /**
     * Remove header/footer image
     *
     * @param string $location
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function removeImage($location = self::IMAGE_HEADER_LEFT)
    {
        if (isset($this->headerFooterImages[$location])) {
            unset($this->headerFooterImages[$location]);
        }
        return $this;
    }

    /**
     * Set header/footer images
     *
     * @param PHPExcel_Worksheet_HeaderFooterDrawing[] $images
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setImages($images)
    {
        if (!is_array($images)) {
            throw new PHPExcel_Exception('Invalid parameter!');
        }

        $this->headerFooterImages = $images;
        return $this;
    }

    /**
     * Get header/footer images
     *
     * @return PHPExcel_Worksheet_HeaderFooterDrawing[]
     */
    public function getImages()
    {
        // Sort array
        $images = array();
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_LEFT])) {
            $images[self::IMAGE_HEADER_LEFT] =         $this->headerFooterImages[self::IMAGE_HEADER_LEFT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_CENTER])) {
            $images[self::IMAGE_HEADER_CENTER] =     $this->headerFooterImages[self::IMAGE_HEADER_CENTER];
        }
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_RIGHT])) {
            $images[self::IMAGE_HEADER_RIGHT] =     $this->headerFooterImages[self::IMAGE_HEADER_RIGHT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_LEFT])) {
            $images[self::IMAGE_FOOTER_LEFT] =         $this->headerFooterImages[self::IMAGE_FOOTER_LEFT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_CENTER])) {
            $images[self::IMAGE_FOOTER_CENTER] =     $this->headerFooterImages[self::IMAGE_FOOTER_CENTER];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_RIGHT])) {
            $images[self::IMAGE_FOOTER_RIGHT] =     $this->headerFooterImages[self::IMAGE_FOOTER_RIGHT];
        }
        $this->headerFooterImages = $images;

        return $this->headerFooterImages;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
