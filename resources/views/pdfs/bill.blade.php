<!-- Format Number -->
<?php 

  // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

  // TO get the first letters
  preg_match_all('/\b\w/', $bill->company->name, $matches); 

  function getIndianCurrency(float $number)
  {
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? " and" . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees Only' : '') . $paise;
  }
?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <title>{{ $settings->bill_format }}{{ $bill->bill_no }}/18-19</title>

    <!-- Style-->
    <style type="text/css">
      
      body {
        font-family: "Times New Roman", Georgia, Serif;  
        //padding-top: 70px;
        font-size: 14px;
      } 

      .header {
        top: 0px;
        position: fixed;
      }

      .footer {
        bottom: 0px;
        position: fixed;
      }

      table {
        width: 100%;
        table-layout: fixed;
      }

      td {
        padding: 2px;
      }

      tr, td {
        border: 1px solid black;
      }

      table {
        border: 1px solid black;
        border-collapse: collapse;
      }

      tr.noBottomBorder td {
        border-bottom-color: transparent;
      }

      tr.noTopBorder td {
        border-top-color: transparent;
      } 

      .wrapper {
        width: 100%;
        padding-bottom: 3px;
      }

      .div-inline {
        display: inline-block; 
        vertical-align: top;
        width: 49.5%; 
      }

      .align-top {
        vertical-align: top;
      }

      .align-bottom {
        vertical-align: bottom;
      }

    </style>

  </head>

  <body> 

    <!-- Header -->
    <!-- <div class="header">
      <img src="" width="100%">
    </div> -->  

    <!-- Bill Heading -->
    <h3 align="center">TAX INVOICE</h3>

    <!-- Basic Details -->
    <div class="wrapper" style="border: 0.1px solid black; border-bottom: 0"> 

      <div class="div-inline">

        <table> 

          <!-- Company Name and Address-->
          <tr>
            <td>
              <b>{{ $bill->company->name }}</b>
              <br>
              {{ $bill->company->address }}
              <br>
              <b>GSTIN/UIN: </b>{{ $bill->company->gstn_no }}
              <br>
              <b>State Code: </b> {{ $bill->company->state_code }}
              <br>
              <b>Email ID:</b> {{ $bill->company->email }}
              <br>
              <b>Mob No. </b> {{ $bill->company->contact1 }}
            </td> 
          </tr>   

          <!-- Consignee Name and Address-->
          <tr>
            <td>
              Consignee
              <br>
              <b>{{ $bill->customer->name }}</b>
              <br>
              {{ $bill->customer->address }}
              <br>
              <b>GSTIN/UIN: </b>{{ $bill->customer->gst_no }}
              <br>
              <b>State Code: </b> {{ $bill->customer->state_code }}
              <br>
              <b>Email ID:</b> {{ $bill->customer->email }}
              <br>
              <b>Mob No. </b>{{ $bill->customer->contact1 }}
            </td> 
          </tr>  

          <!-- Buyer Name and Address-->
          <tr>
            <td style="border-bottom: 0">
              Buyer (if other than consignee)
              <br>
              <b>{{ $bill->customer->name }}</b>
              <br>
              {{ $bill->customer->address }}
              <br>
              <b>GSTIN/UIN: </b>{{ $bill->customer->gst_no }}
              <br>
              <b>State Code: </b> {{ $bill->customer->state_code }}
              <br>
              <b>Email ID:</b> {{ $bill->customer->email }}
              <br>
              <b>Mob No. </b>{{ $bill->customer->contact1 }}
            </td> 
          </tr>  
 
        </table>  
        
      </div>

      <div class="div-inline">

        <table>

          <!-- Invoice No -->
          <tr>
            <td>
              Invoice No
              <br>
              <b>{{ $settings->bill_format }}{{ $bill->bill_no }}/18-19</b>
            </td>
            <td>
              Invoice Date
              <br>
              <b>{{ Carbon\Carbon::parse($bill->created_at)->format('d-m-Y') }} </b>
            </td>
          </tr> 

          <tr>
            <td>
              Delivery Note
              <br>
              <b>{{ $bill->delivery_note }}</b>
            </td>
            <td>
              Mode/Terms of payment
              <br>
              <b>{{ $bill->terms_of_payment }}</b>
            </td> 
          </tr>

          <tr>
            <td>
              Supplier's Ref.
              <br>
              <b>{{ $bill->supplier_reference }}</b>
            </td>
            <td>
              Other Reference(s)
              <br>
              <b></b>
            </td>
          </tr>

          <tr>
            <td>
              Buyer's Order No
              <br>
              <b>{{ $bill->buyer_order_no }}</b>
            </td>
            <td>
              Dated
              <br>
              <b></b>
            </td> 
          </tr>

          <tr>
            <td>
              Dispatch Document No.
              <br>
              <b>{{ $bill->despatch_document_no }}</b>
            </td>
            <td>
              Delivery Note Date
              <br>
              <b>{{ $bill->delivery_note_date }}</b>
            </td> 
          </tr>

          <tr>
            <td>
              Dispatch through
              <br>
              <b>{{ $bill->despatch_through }}</b>
            </td>
            <td>
              Destination
              <br>
              <b>{{ $bill->destination }}</b>
            </td>
          </tr>

          <tr>
            <td colspan="2" rowspan="5">
              Terms of delivery
              <br>
              <b>{{ $bill->terms_of_delivery }}</b>
            </td> 
          </tr>

        </table>
        
      </div>

    </div>  

    <!-- Charges Details -->
    <!-- <h4><u>Bill Description as follows:</u></h4> -->
    <div class="wrapper">

      <table>
        <!-- Heading   -->
        <tr align="center">
          <td>
            <b>Sr. No.</b>
          </td>
          <td colspan="3">
            <b>Description of Goods</b>
          </td>
          <td>
            <b>HSN Code</b>
          </td>
          <td>
            <b>Quantity</b>
          </td> 
          <td>
            <b>Rate</b>
          </td>
          <td>
            <b>per</b>
          </td>
          <td>
            <b>Amount</b>
          </td>
        </tr>

        <!-- Billing details -->
        @foreach($bill->billing_details as $billing_detail)
        <tr align="center" class="noBottomBorder"> 

            <!-- Sr. No -->
            <td style="height:40px;" class="align-top">
              {{ $loop->index + 1 }}
            </td>

            <!-- Description -->
            <td colspan="3">
              {{ $billing_detail->product_category->name }}
            </td>

            <!-- HSN Code -->
            <td>
              {{ $billing_detail->product_category->hsn_code }}
            </td>

            <!-- Quantity -->
            <td>
              {{ $billing_detail->qty }} Bag
            </td>

            <!-- Cose per unit -->
            <td>
              Rs. {{ number_format($billing_detail->amount / $billing_detail->qty, 2, '.', '') }}
            </td>

            <!-- Rate per bag -->
            <td>
              Bags
            </td>

            <!-- Amount -->
            <td>
              Rs. {{ number_format($billing_detail->amount, 2, '.', '') }}
            </td>

        </tr>
        @endforeach  

        <!-- Tax -->
        <tr>
          <td></td>
          <td colspan="3" align="right" >
            @if($bill->customer->state_code == $bill->company->state_code)
              @foreach($bill->billing_taxes as $billing_tax)
                <b>Output SGST {{ $billing_tax->tax->tax_percent/2 }}%</b>
                <br>
                <b>Output CGST {{ $billing_tax->tax->tax_percent/2 }}%</b>
              @endforeach
            @else
              @foreach($bill->billing_taxes as $billing_tax)
                <b>Output IGST {{ $billing_tax->tax->tax_percent }}%</b>
              @endforeach
            @endif
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td align="center">
            @if($bill->customer->state_code == $bill->company->state_code)
              @foreach($bill->billing_taxes as $billing_tax)
                <b>Rs. {{ number_format($billing_tax->amount/2, 2, '.', '') }}</b>
                <br>
                <b>Rs. {{ number_format($billing_tax->amount/2, 2, '.', '') }}</b>
              @endforeach
            @else
              @foreach($bill->billing_taxes as $billing_tax)
                <b>Rs. {{ number_format( $billing_tax->amount, 2, '.', '' ) }}</b>
              @endforeach
            @endif
          </td>
        </tr>

        <!-- View the total -->
        <tr>
          <td></td>
          <td colspan="3" align="right">
            Total
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td align="center">
            <?php $total_tax = 0; ?>
            @foreach($bill->billing_taxes as $billing_tax)
              <?php $total_tax += $billing_tax->amount ?>
            @endforeach
            <?php $total = $bill->sub_total + $total_tax ?>
            Rs. {{ number_format( $total, 2, '.', '') }}
          </td>
        </tr>

        <!-- Amount in words -->
        <tr>
          <td colspan="9">
            Amount Chargeable (in words)
            <br>
            <b>{{ getIndianCurrency($total) }}</b>
          </td>
        </tr>

        <!-- Taxable amount -->
        <tr align="center">
          @if($bill->customer->state_code == $bill->company->state_code)
            <td colspan="3"  rowspan="2">
          @else
            <td colspan="5" rowspan="2">
          @endif
            HSN/SAC
          </td>
          <td rowspan="2">
            Taxable Value
          </td>
          @if($bill->customer->state_code == $bill->company->state_code)
            <td colspan="2">
              CGST Tax
            </td>
            <td colspan="2">
              SGST Tax
            </td>
          @else
            <td colspan="2">
              Integrated Tax
            </td>
          @endif
          <td rowspan="2">
            Total Tax Amount
          </td>
        </tr>
        <tr align="center">
          <td>
            Rate
          </td>
          <td>
            Amount
          </td>
          @if($bill->customer->state_code == $bill->company->state_code)
            <td>
              Rate
            </td>
            <td>
              Amount
            </td>
          @endif
        </tr>

        <!-- Billing details -->
        <?php
          $total_taxable = 0;
          $total_tax = 0;
        ?>
        @foreach($bill->billing_details as $billing_detail)
        <tr align="center">  

          <!-- HSN Code -->
          @if($bill->customer->state_code == $bill->company->state_code)
            <td colspan="3">
          @else
            <td colspan="5">
          @endif
            {{ $billing_detail->product_category->hsn_code }}
          </td> 

          <!-- Taxable Amount -->
          <td>
            <?php  
              $total_taxable += $billing_detail->amount;
            ?>
            Rs. {{ number_format($billing_detail->amount, 2, '.', '') }}
          </td>

          <!-- Rate -->
          <!-- Tax amount --> 
          <?php 
            $tax = 0;
            foreach($bill->billing_taxes as $billing_tax) {
              $tax = $billing_detail->amount * $billing_tax->tax->tax_percent / 100;
              $total_tax += $tax;
            }
          ?>
          @if($bill->customer->state_code == $bill->company->state_code)
            @foreach($bill->billing_taxes as $billing_tax)
            <td>
              {{ $billing_tax->tax->tax_percent/2 }} %
            </td>
            <td>
              Rs. {{ number_format($billing_tax->amount/2, 2, '.', '') }}</b>
            </td>
            <td>
              {{ $billing_tax->tax->tax_percent/2 }} %
            </td>
            <td>
              Rs. {{ number_format($billing_tax->amount/2, 2, '.', '') }}</b>
            </td>
            @endforeach
          @else
            @foreach($bill->billing_taxes as $billing_tax)
              <td>
                {{ $billing_tax->tax->tax_percent }} %
              </td>
              <td>
                Rs. {{ number_format($billing_tax->amount, 2, '.', '') }}</b>
              </td>
            @endforeach
          @endif 

          <!-- Total tax amount -->
          <td> 
            Rs. {{ number_format( $tax, 2, '.', '' ) }}
          </td>

        </tr>
        @endforeach  

        <tr>
          <td colspan="5" align="right">
            <b>Total</b>
          </td>
          <td align="center">
            <b>Rs. {{ number_format($total_taxable, 2, '.', '') }}</b>
          </td>
          <td></td>
          <td></td>
          <td align="center">
            <b>Rs. {{ number_format($total_tax, 2, '.', '') }}</b>
          </td>
        </tr>

        <!-- Total tax amount -->
        <tr>
          <td colspan="9">
            Tax Chargeable (in words) : <b>{{ getIndianCurrency($total_tax) }}</b> 
          </td>
        </tr> 

      </table>
      
    </div>  

    <!-- Terms -->
    <div class="wrapper">

      <table>

        <tr>
          
          <td>

            <b>Declaration </b> 
            <br>
            We declare that this invoice shows the actual price of the goods described & that all particukars are true and correct

            1) Goods once sold will not taken back in any case. 2) We are not responsibe for breakage, leakage, shortage or loss in transit as goods are delivered carefully checked, packed & delivered. 3) No complaint if any will be entertained after 3 days from received. 4) Subject to Ankleshwar Jurisdiction. 5) Interest 24% will be charged for all unpaid bills after due date. 6) If any change in Tax rate due to HSN Code Change than accordingly tax amount will be refunded/ recovered. 

          </td>

          <td style="vertical-align: top" align="center">
            <small>
              Certified that the particular given above are true and correct
            </small>
            <br>
            <b>For {{ strtoupper($bill->company->name) }}</b>
            <br>
            <br> 
            <br> 
            <label>
              <b>Authorized Signatory</b>
            </label>
          </td>

        </tr>  

      </table>
      
    </div> 
  
  </body>

</html>