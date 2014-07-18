function plural( n, one, one_, many ) {

   return (  
        ( n % 10 == 1 )
             ?
                ( n % 100 == 11 ? many : one )
             :
                (
          ( n % 10 == 2 || n % 10 == 3 || n % 10 == 4 )
           ?
            ( ( n % 100 == 2 || n % 100 == 3 || n % 100 == 4 )
             ?
              many
             :
              one_
            )
           :
            many
        )
    );
}