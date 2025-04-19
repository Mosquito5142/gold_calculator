<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รูปทรงขนาดจริง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .shape-container {
            position: relative;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #shape {
            background-color: #4a90e2;
            border: 2px solid #000;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            margin: 1rem;
        }

        .width-label {
            position: absolute;
            top: 0;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
        }

        .height-label {
            position: absolute;
            right: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem;
            padding-left: 0.5rem;
        }

        .shape-wrapper {
            position: relative;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="card shadow p-4 mx-auto" style="max-width: 480px;">
            <div class="row">
                <!-- Controls remain the same -->
                <div class="col-6 mb-3">
                    <label class="form-label">รูปทรง:</label>
                    <select id="shapeType" class="form-select">
                        <option value="square">สี่เหลี่ยม</option>
                        <option value="circle">วงกลม</option>
                    </select>
                </div>

                <div class="col-6 mb-3">
                    <label class="form-label">หน่วย:</label>
                    <select id="unitSelect" class="form-select">
                        <option value="mm" selected>มิลลิเมตร (mm)</option>
                        <option value="cm">เซนติเมตร (cm)</option>
                    </select>
                </div>

                <div class="col-6 mb-3">
                    <label class="form-label">ความกว้าง:</label>
                    <input type="number" id="widthInput" class="form-control" value="50" min="0.1" step="0.1">
                </div>

                <div class="col-6 mb-3" id="heightContainer">
                    <label class="form-label">ความสูง:</label>
                    <input type="number" id="heightInput" class="form-control" value="50" min="0.1" step="0.1">
                </div>
            </div>

            <div class="d-grid mb-3">
                <button onclick="drawShape()" class="btn btn-primary">แสดง</button>
            </div>

            <div class="shape-container">
                <div class="width-label">
                    ← <span id="labelWidth"></span> →
                </div>
                <div class="shape-wrapper">
                    <div id="shape"></div>
                    <div class="height-label">
                        ↑<br><span id="labelHeight"></span><br>↓
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript remains the same
        const shape = document.getElementById('shape');
        const shapeType = document.getElementById('shapeType');
        const widthInput = document.getElementById('widthInput');
        const heightInput = document.getElementById('heightInput');
        const heightContainer = document.getElementById('heightContainer');
        const unitSelect = document.getElementById('unitSelect');

        let pixelsPerCM = localStorage.getItem('pixelsPerCM') || 36.5;

        function drawShape() {
            const isMM = unitSelect.value === 'mm';
            const factor = isMM ? 0.1 : 1;
            const widthCM = parseFloat(widthInput.value) * factor;
            const heightCM = shapeType.value === 'circle' ? widthCM : parseFloat(heightInput.value) * factor;

            const widthPx = widthCM * pixelsPerCM;
            const heightPx = heightCM * pixelsPerCM;

            shape.style.width = widthPx + 'px';
            shape.style.height = heightPx + 'px';
            shape.style.borderRadius = shapeType.value === 'circle' ? '50%' : '0';

            const unitLabel = unitSelect.value;
            document.getElementById('labelWidth').textContent = `${widthInput.value} ${unitLabel}`;
            document.getElementById('labelHeight').textContent = `${shapeType.value === 'circle' ? widthInput.value : heightInput.value} ${unitLabel}`;
        }

        shapeType.addEventListener('change', () => {
            heightContainer.style.display = shapeType.value === 'circle' ? 'none' : 'block';
            drawShape();
        });

        drawShape();
    </script>
</body>
</html>