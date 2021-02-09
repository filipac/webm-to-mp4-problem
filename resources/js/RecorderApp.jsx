import axios from "axios";
import { useCallback, useState } from "react";
import VideoRecorder from "react-video-recorder";
import styled from "styled-components";

const MainDiv = styled.div`
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    .record-video-box {
        width: 300px;
        height: 300px;
    }

    .submit-btn {
        padding: 10px;
        margin-top: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }
`;

export const RecorderApp = () => {
    const [file, setFile] = useState(null);

    const send = useCallback(async () => {
        let data = new FormData();
        data.append("file", file);
        let resp = await axios.post("/upload-endpoint", data, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        window.location = `/view/${resp.data.id}`;
    }, [file]);
    return (
        <MainDiv>
            <div className="record-video-box">
                <VideoRecorder
                    showReplayControls
                    replayVideoAutoplayAndLoopOff
                    onCameraOn={(e) => {
                        setFile(null);
                    }}
                    onRecordingComplete={(videoBlob) => {
                        setFile(videoBlob);
                    }}
                    renderUnsupportedView={() => (
                        <div>
                            The browser is incapable of recording a video.
                            Please try to upload from your files or use the
                            Comments box to submit your question.
                        </div>
                    )}
                />
            </div>
            {file && (
                <div>
                    <button className="submit-btn" onClick={send}>
                        Submit recording to server
                    </button>
                </div>
            )}
        </MainDiv>
    );
};
