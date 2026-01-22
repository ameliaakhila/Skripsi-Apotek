graph TD
    %% START & END
    S((Start))
    E((End))

    %% NODES
    A1((1<br/>Identifikasi Masalah<br/>6))
    A2((2<br/>Analisis Sistem Berjalan<br/>8))
    A3((3<br/>Analisis Kebutuhan Sistem<br/>8))
    A4((4<br/>Pemodelan Sistem<br/>8))
    A5((5<br/>Perancangan Basis Data<br/>7))
    A6((6<br/>Rancangan Tampilan<br/>6))
    A7((7<br/>Implementasi Sistem<br/>14))
    A8((8<br/>Uji Coba Sistem<br/>6))
    A9((9<br/>Perbaikan Sistem<br/>5))
    A10((10<br/>Implementasi Pengaduan<br/>2))
    A11((11<br/>Dokumentasi<br/>1))

    %% FLOWS
    S --> A1
    A1 --> A2
    A2 --> A3
    A3 --> A4
    A4 --> A5
    A4 --> A6
    A5 --> A7
    A6 --> A7
    A7 --> A8
    A8 --> A9
    A9 --> A10
    A10 --> A11
    A11 --> E

    %% CRITICAL PATH (MERAH)
    class A1,A2,A3,A4,A5,A7,A8,A9,A10,A11 critical

    %% STYLES
    classDef critical fill:#ffe6e6,stroke:#ff0000,stroke-width:3px,color:#000
    classDef normal fill:#e6f2ff,stroke:#1f4fd8,stroke-width:2px,color:#000