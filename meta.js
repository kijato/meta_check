
  function fill_korzetek() {
	 var veletlen    = Math.random;
	 $.get("get_korzet_lista.php",
           { sid: veletlen },
           function(valasz) { $("#korzetek").html(valasz); }
      );
	 //$("#korzetek").off(mousemove);
  }

  function fill_telepulesek() {
	 var veletlen    = Math.random;
	 var korzet = $("#korzetek").val();
	 $.get("get_telepules_lista.php",
           { korzet: korzet, sid: veletlen },
           function(valasz) { $("#telepulesek").html(valasz); }
      );
	 $("#telepulesek").prop('disabled', false);
	 $("#capt").html("Üres adathalmaz..."); 
	 $("#min_max").html("Üres adathalmaz..."); 
  }

  function fill_fekvesek() {
	 var veletlen    = Math.random;
	 var korzet = $("#korzetek").val();
	 var telepules = $("#telepulesek").val();
	 $.get("get_fekves_lista.php",
           { korzet: korzet, telepules: telepules, sid: veletlen },
           function(valasz) { $("#fekvesek").html(valasz); }
      );
	  //$("#fekvesek").prop('disabled', false);
	  $("#hrsz_tol").prop('disabled', false);
	  $("#hrsz_ig").prop('disabled', false);
  }

  function get_rows() {
	 var veletlen    = Math.random;
	 var korzet = $("#korzetek").val();
	 var telepules = $("#telepulesek").val();
	 $.get("get_meta_sorok.php",
           { korzet: korzet, telepules: telepules, sid: veletlen },
           function(valasz) { $("#capt").html(valasz); }
      );
	 $.get("get_meta_hiany.php",
           { korzet: korzet, telepules: telepules, sid: veletlen },
           function(valasz) { $("#capt2").html(valasz); }
      );
  }

  function set_hrsz_ig() {
	  var oldValue = $("#hrsz_tol").val();
	  if ( oldValue > 1 ) {
	    $("#hrsz_ig").val( ++oldValue )
	  }
	  $("#min_max").html("Egy kis türelmet kérek...")
	  fill_min_max();
  }
  
  function set_hrsz_tol() {
	  $("#min_max").html("Egy kis türelmet kérek...")
	  fill_min_max();
  }
  
  function fill_min_max() {
	 var veletlen  = Math.random;
	 var korzet    = $("#korzetek").val();
	 var telepules = $("#telepulesek").val();
	 var fekves    = $("#fekvesek").val();
	 var hrsz_tol  = $("#hrsz_tol").val();
	 var hrsz_ig   = $("#hrsz_ig").val();
	 $.get("get_min_max.php",
           { korzet: korzet, telepules: telepules, fekves: fekves, hrsz_tol: hrsz_tol, hrsz_ig: hrsz_ig, sid: veletlen },
           function(valasz) { $("#min_max").html(valasz); }
      );
  }
  
   $(document).ready( function() {
      //$("#korzetek").mousemove(fill_korzetek);
      fill_korzetek();
      $("#korzetek").change(fill_telepulesek);
      $("#telepulesek").change(fill_fekvesek);
      $("#telepulesek").change(get_rows);
      $("#fekvesek").change(fill_min_max);
	  $("#hrsz_tol").keyup(set_hrsz_ig);
	  $("#hrsz_tol").change(set_hrsz_ig);
	  $("#hrsz_ig").keyup(set_hrsz_tol);
	  $("#hrsz_ig").change(set_hrsz_tol);
   });

   
